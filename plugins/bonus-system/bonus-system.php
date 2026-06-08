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

use BonusSystem\Controllers\SettingsController;
use BonusSystem\Parsers\ApplyingBestParser;
use BonusSystem\Parsers\SalesBonusParser;
use BonusSystem\Services\SettingsService;
use BonusSystem\Views\SettingView;

if (!defined("ABSPATH")) {
    exit;
}

spl_autoload_register(function ($class) {
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
        $this->settings_controller = new SettingsController(
            new SettingsService(new ApplyingBestParser(), new SalesBonusParser()),
            new SettingView()
        );
    }

    private function register_hooks(): void
    {
        add_action("admin_menu", [$this->settings_controller, "register_editing_page"], 100);
        add_action("admin_init", [$this->settings_controller, "register_settings"]);
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