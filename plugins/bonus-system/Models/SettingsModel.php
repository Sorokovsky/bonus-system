<?php
namespace BonusSystem\Models;

class SettingsModel
{
    const OPTION_NAME = 'bonus_system_settings';

    public function get_settings(): array
    {
        $defaults = $this->get_defaults();
        $settings = get_option(self::OPTION_NAME, $defaults);
        return array_merge($defaults, $settings);
    }

    public function get_tiers(): array
    {
        $settings = $this->get_settings();
        return $settings['tiers'] ?? [];
    }

    public function update_tiers(array $tiers): bool
    {
        $settings = $this->get_settings();
        $settings['tiers'] = $tiers;
        return update_option(self::OPTION_NAME, $settings);
    }

    public function get_defaults(): array
    {
        return [
            'enabled' => true,
            'tiers' => [],
            'settings' => [
                'apply_best' => true,
                'show_notice' => true
            ]
        ];
    }

    public function init_default_settings(): void
    {
        if (get_option(self::OPTION_NAME) === false) {
            add_option(self::OPTION_NAME, $this->get_defaults());
        }
    }
}