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
}