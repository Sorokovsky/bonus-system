<?php

namespace BonusSystem\Services;

use BonusSystem\Models\SalesBonus;

class BonusesService
{
    private SettingsService $settings_service;

    public function __construct(SettingsService $settings_service)
    {
        $this->settings_service = $settings_service;
    }

    /**
     * @return array<SalesBonus>
     */
    public function get_all(): array
    {
        $settings = $this->settings_service->get_settings();
        $sales = $settings->get_sales();
        return array_merge($sales);
    }

    public function apply(): void
    {
        $sales = $this->get_all();
        usort($sales, function ($a, $b) {
            return $a->get_min_price() <=> $b->get_min_price();
        });

        $best_bonus = null;
        foreach ($sales as $sale) {
            if ($sale->can_activate()) {
                $best_bonus = $sale;
            }
        }
        if ($best_bonus) {
            $best_bonus->activate();
        }
    }
}