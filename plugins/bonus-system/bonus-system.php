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
    }

    public function force_reload(): void
    {
        if (!is_cart()) {
            return;
        }
        wp_add_inline_script('jquery', '
        (function() {
            let isReloading = false;
            
            function reloadPage() {
                if (!isReloading) {
                    isReloading = true;
                    window.location.reload();
                }
            }
            
            // 1. Перехоплюємо всі fetch запити
            const originalFetch = window.fetch;
            window.fetch = function() {
                const url = arguments[0];
                const promise = originalFetch.apply(this, arguments);
                
                // Перевіряємо, чи це запит до WooCommerce
                if (typeof url === "string" && (url.includes("wc/store") || url.includes("cart") || url.includes("wc/"))) {
                    promise.then(function(response) {
                        if (response.ok && !isReloading) {
                            reloadPage();
                        }
                    }).catch(function() {});
                }
                return promise;
            };
            
            // 2. Перехоплюємо XMLHttpRequest
            const originalOpen = XMLHttpRequest.prototype.open;
            const originalSend = XMLHttpRequest.prototype.send;
            
            XMLHttpRequest.prototype.open = function() {
                this._url = arguments[1];
                return originalOpen.apply(this, arguments);
            };
            
            XMLHttpRequest.prototype.send = function() {
                const url = this._url;
                this.addEventListener("load", function() {
                    if (this.status === 200 && url && (url.includes("wc/store") || url.includes("cart") || url.includes("wc/"))) {
                        if (!isReloading) {
                            reloadPage();
                        }
                    }
                });
                return originalSend.apply(this, arguments);
            };
        })();
    ');
    }

    public function override_cart(string $content): string
    {
        if (is_cart()) {
            WC()->cart->calculate_totals();
            do_action('woocommerce_cart_calculate_fees', WC()->cart);
            return $this->bonuses_controller->cart_page() . $content;
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