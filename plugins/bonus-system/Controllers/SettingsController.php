<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\SettingsService;
use BonusSystem\Views\SettingView;

class SettingsController
{
    private SettingsService $service;
    private SettingView $view;

    public function __construct(SettingsService $service, SettingView $view) {
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
}