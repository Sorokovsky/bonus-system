<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\SettingsService;
use BonusSystem\Views\Editor\SettingView;

class SettingsController
{
    private SettingsService $service;
    private SettingView $view;

    public function __construct(SettingsService $service, SettingView $view)
    {
        $this->service = $service;
        $this->view = $view;
    }

    public function register_editing_page(): void
    {
        add_submenu_page(
            "woocommerce",
            __("Система бонусів", 'bonus-system'),
            __("Система бонусів", 'bonus-system'),
            'manage_options',
            SettingsService::OPTION_NAME,
            [$this, 'render_editor_page']
        );
    }

    public function render_editor_page(): void
    {
        $this->view->render($this->service->get_settings());
    }

    public function register_settings(): void
    {
        register_setting(
            SettingsService::OPTION_NAME,
            SettingsService::OPTION_NAME,
            [
                'type' => 'array',
                'sanitize_callback' => [$this->service, 'sanitize_settings'],
                'default' => [],
            ]
        );

        add_settings_section(
            'bonus-main-section',
            __("Налаштування", 'bonus-system'),
            null,
            SettingsService::OPTION_NAME
        );
        add_settings_field(
            'bonus_sales_fields',
            __("Бонусні рівні", 'bonus-system'),
            [$this, 'render_sales_fields'],
            SettingsService::OPTION_NAME,
            'bonus-main-section',
            ['settings' => $this->service->get_settings()]
        );
    }

    public function render_sales_fields(): void
    {
        $settings = $this->service->get_settings();
        $this->view->render_sales_fields($settings);
    }
}