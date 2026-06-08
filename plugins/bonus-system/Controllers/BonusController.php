<?php
namespace BonusSystem\Controllers;

use BonusSystem\Models\SalesBonusModel;
use BonusSystem\Services\DiscountService;
use BonusSystem\Repositories\BonusRepository;

class BonusController
{
    private DiscountService $service;
    private BonusRepository $repository;

    public function __construct()
    {
        $this->repository = new BonusRepository();
        $this->service = new DiscountService($this->repository);
    }

    public function apply_discount(): void
    {
        if (!$this->validate_cart()) {
            return;
        }
        $best_bonus = $this->service->find_best_bonus();
        if ($best_bonus) {
            $this->add_discount_to_cart($best_bonus);
        }
    }

    public function ajax_get_bonuses(): void
    {
        check_ajax_referer('bonus_system_nonce', 'nonce');
        $bonuses = $this->repository->get_all_bonuses();

        wp_send_json_success([
            'bonuses' => array_map(function ($bonus) {
                return [
                    'min_price' => $bonus->get_min_price(),
                    'discount_type' => $bonus->get_discount_type(),
                    'discount_value' => $bonus->get_discount_value()
                ];
            }, $bonuses)
        ]);
    }

    private function validate_cart(): bool
    {
        return function_exists('WC') && WC()->cart !== null;
    }

    private function add_discount_to_cart(SalesBonusModel $bonus): void
    {
        add_action('woocommerce_cart_calculate_fees', function ($cart) use ($bonus) {
            $subtotal = $cart->get_subtotal();
            $discount = $bonus->calculate_discount($subtotal);

            if ($discount > 0) {
                $cart->add_fee($bonus->get_discount_label(), -$discount);
            }
        });
    }
}