<?php

namespace BonusSystem\Services;

use BonusSystem\Models\Bonus;
use BonusSystem\Models\SalesBonus;
use BonusSystem\Models\TextBonus;

class BonusesService
{
    private SettingsService $settings_service;

    /**
     * @var array<Bonus>
     */
    private array $activated_bonues;

    public function __construct(SettingsService $settings_service)
    {
        $this->settings_service = $settings_service;
        $this->activated_bonues = [];
    }

    /**
     * Summary of get_activated_bonuses
     * @return array<Bonus>
     */
    public function get_activated_bonuses(): array
    {
        return $this->activated_bonues;
    }

    /**
     * Summary of get_sales
     * @return array<SalesBonus>
     */
    public function get_sales(): array
    {
        return $this->settings_service->get_settings()->get_sales();
    }

    /**
     * Summary of get_texts
     * @return array<TextBonus>
     */
    public function get_texts(): array
    {
        return $this->settings_service->get_settings()->get_texts();
    }

    public function apply(): void
    {
        $this->apply_sales();
        $this->apply_texts();
    }

    private function apply_texts(): void
    {
        $texts = $this->get_texts();
        foreach ($texts as $text) {
            if ($text->can_activate()) {
                $text->activate();
                $this->activated_bonues[] = $text;
            }
        }
    }

    private function apply_sales(): void
    {
        $sales = $this->get_sales();
        usort($sales, fn($a, $b) => $a->get_min_price() <=> $b->get_min_price());

        $best_bonus = null;
        foreach ($sales as $sale) {
            if ($sale->can_activate()) {
                $best_bonus = $sale;
            }
        }
        if ($best_bonus) {
            $best_bonus->activate();
            $this->activated_bonues[] = $best_bonus;
        }
    }
}