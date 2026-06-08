<?php
namespace BonusSystem\Controllers;

use BonusSystem\Models\SettingsModel;
use BonusSystem\Views\SettingsView;

class SettingsController
{
    private SettingsModel $model;
    private SettingsView $view;

    public function __construct()
    {
        $this->model = new SettingsModel();
        $this->view = new SettingsView();
    }

    public function add_admin_menu(): void
    {
        add_submenu_page(
            'woocommerce',
            __("Система бонусів", "bonus-system"),
            __('Бонуси', 'bonus-system'),
            'manage_options',
            'bonus-system-settings',
            [$this, 'render_settings_page'],
            20
        );
    }

    public function render_settings_page(): void
    {
        $settings = $this->model->get_settings();
        $this->view->render($settings);
    }

    public function register_settings(): void
    {
        register_setting(
            'bonus_system_settings',
            SettingsModel::OPTION_NAME,
            [$this, 'validate_settings']
        );
    }

    public function validate_settings(array $input): array
    {
        $output = $this->model->get_defaults();
        if ($input['enabled']) {
            $output['enabled'] = (bool) $input['enabled'];
        }
        if (isset($input['tiers']) && is_array($input['tiers'])) {
            $output['tiers'] = $this->validate_tiers($input['tiers']);
        }
        return $output;
    }

    private function validate_tiers(array $tiers): array
    {
        $validated = [];

        foreach ($tiers as $tier) {
            if (isset($tier['min_amount']) && $tier['min_amount'] > 0) {
                $discount_type = sanitize_text_field($tier['discount_type']);
                $discount_value = 0;
                if ($discount_type === 'percent') {
                    $discount_value = isset($tier['discount_percent']) ? (float) $tier['discount_percent'] : 0;
                } else {
                    $discount_value = isset($tier['discount_fixed']) ? (float) $tier['discount_fixed'] : 0;
                }

                if ($discount_value == 0 && isset($tier['discount_value'])) {
                    $discount_value = (float) $tier['discount_value'];
                }
                $validated[] = [
                    'min_amount' => (float) $tier['min_amount'],
                    'discount_type' => sanitize_text_field($tier['discount_type']),
                    'discount_percent' => $discount_type === 'percent' ? $discount_value : null,
                    'discount_fixed' => $discount_type === 'fixed' ? $discount_value : null,
                ];
            }
        }

        return $validated;
    }

    public function ajax_update_settings(): void
    {
        check_ajax_referer('bonus_system_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Недостатньо прав');
        }

        $tiers = json_decode(stripslashes($_POST['tiers']), true);

        if ($this->model->update_tiers($tiers)) {
            wp_send_json_success('Налаштування збережено');
        }

        wp_send_json_error('Помилка збереження');
    }
}