<?php

namespace BonusSystem\Services;

use BonusSystem\Models\Bonus;
class BonusesService
{
    private SettingsService $settings_service;
    private CartSaleService $sale_service;

    /**
     * @var array<Bonus>
     */
    private array $bonuses;

    public function __construct(SettingsService $settings_service, CartSaleService $cart_sale_service)
    {
        $this->settings_service = $settings_service;
        $this->sale_service = $cart_sale_service;
        $settings = $this->settings_service->get_settings();
        $all_bonuses = array_merge($settings->get_sales(), $settings->get_texts());
        usort($all_bonuses, fn($a, $b) => $a->get_min_price() <=> $b->get_min_price());
        $this->bonuses = $all_bonuses;
    }

    /**
     * Summary of get_activated_bonuses
     * @return array<Bonus>
     */
    public function get_activated_bonuses(): array
    {
        $result = [];
        foreach ($this->bonuses as $bonus) {
            if ($bonus->can_activate()) {
                $result[] = $bonus;
            }
        }
        return $result;
    }

    /**
     * Summary of get_all
     * @return array<Bonus>
     */
    public function get_all(): array
    {
        return $this->bonuses;
    }

    public function get_next_bonus(): Bonus|null
    {
        $next = null;
        foreach ($this->bonuses as $bonus) {
            if (!$bonus->can_activate()) {
                $next = $bonus;
                break;
            }
        }
        return $next;
    }

    public function get_difference(): float
    {
        $subtotal = WC()->cart->get_subtotal();
        $next = $this->get_next_bonus();
        if ($next === null) {
            return 0;
        }
        return $next->get_min_price() - $subtotal;
    }

    public function get_percent(): float
    {
        $bonus = $this->get_next_bonus();
        if ($bonus === null) {
            return 100;
        }
        $subtotal = WC()->cart->get_subtotal();
        return min(($subtotal * 100) / $bonus->get_min_price(), 100);
    }

    public function apply(): void
    {
        foreach ($this->bonuses as $bonus) {
            if ($bonus->can_activate()) {
                $bonus->activate();
            }
        }
    }

    public function has_sale_product(): bool
    {
        return $this->sale_service->has_sale_product();
    }
}