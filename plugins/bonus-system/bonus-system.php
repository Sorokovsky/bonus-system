<?php
/*
Plugin Name: Система бонусів
Description: Система бонусів для інтернет магазинів.
Version: 0.0.1
Requires at least: 5.8
Requires PHP: 7.2
Requires Plugins: woocommerce
Author: Sorokovskys
Text Domain: bonus-system
*/

namespace BonusSystem;

use BonusSystem\Controllers\BonusesController;
use BonusSystem\Controllers\SettingsController;
use BonusSystem\Parsers\ApplyingBestParser;
use BonusSystem\Parsers\SalesBonusParser;
use BonusSystem\Parsers\TextsParser;
use BonusSystem\Services\BonusesService;
use BonusSystem\Services\SettingsService;
use BonusSystem\Views\Client\CartView;
use BonusSystem\Views\Editor\SettingView;

if (!defined("ABSPATH")) {
    exit;
}

spl_autoload_register(function (string $class) {
    $prefix = 'BonusSystem\\';
    $base_dir = __DIR__ . '/';
    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }
    $relative_class = substr($class, $length);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("BonusSystem: Файл не знайдено - " . $file);
    }
});

class BonusSystemPlugin
{
    private static ?BonusSystemPlugin $instance = null;

    private SettingsController $settings_controller;

    private BonusesController $bonuses_controller;

    public static function get_instance(): BonusSystemPlugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init_controllers();
        $this->register_hooks();
    }

    private function init_controllers(): void
    {
        $service = new SettingsService(
            new ApplyingBestParser(),
            new SalesBonusParser(),
            new TextsParser()
        );
        $this->settings_controller = new SettingsController(
            $service,
            new SettingView()
        );
        $this->bonuses_controller = new BonusesController(
            new BonusesService($service),
            new CartView()
        );
    }

    private function register_hooks(): void
    {
        add_action("admin_menu", [$this->settings_controller, "register_editing_page"], 100);
        add_action("admin_init", [$this->settings_controller, "register_settings"]);
        add_action("woocommerce_calculate_fees", [$this->bonuses_controller, 'apply'], 10, 1);
        add_action("woocommerce_cart_loaded_from_session", [$this->bonuses_controller, 'apply'], 10, 1);
        add_filter("the_content", [$this, 'override_cart'], 999);
        add_action('wp_enqueue_scripts', [$this, 'force_reload']);
        add_action('woocommerce_checkout_order_processed', [$this->bonuses_controller, 'save_bonuses'], 10, 2);
    }

    public function force_reload(): void
    {
        if (!is_cart()) {
            return;
        }
        wp_enqueue_script(
            'force_reload',
            plugin_dir_url(__FILE__) . "assets/js/force-reload.js",
            ['jquery'],
            '1.0.0'
        );
    }

    public function override_cart(string $content): string
    {
        try {
            if (is_cart()) {
                WC()->cart->calculate_totals();
                do_action('woocommerce_cart_calculate_fees', WC()->cart);
                return $this->bonuses_controller->cart_page() . $content;
            }
        } catch (\Throwable $ex) {
        }
        return $content;
    }

    public function activate()
    {
    }

    public function deactivate()
    {

    }
}

$plugin = BonusSystemPlugin::get_instance();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);