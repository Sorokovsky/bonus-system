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

require_once plugin_dir_path(__FILE__) . "sales-bonus.php";
require_once plugin_dir_path(__FILE__) . "page-settings.php";

class BonusSystem
{
    private PageSettings $page_settings;

    public function __construct()
    {
        add_action("woocommerce_cart_loaded_from_session", [$this, 'discount']);
        $this->page_settings = new PageSettings();
    }

    private function get_sales()
    {
        $tiers = get_option('bonus_system_tiers', []);

        if (empty($tiers)) {
            return [];
        }

        $sales = [];
        foreach ($tiers as $tier) {
            // Перевіряємо чи є мінімальна сума
            if (empty($tier['min_amount'])) {
                continue;
            }

            // Отримуємо значення знижки в залежності від типу
            $discount_type = isset($tier['discount_type']) ? $tier['discount_type'] : 'percent';
            $discount_value = 0;

            if ($discount_type === 'percent') {
                if (isset($tier['discount_percent']) && !empty($tier['discount_percent'])) {
                    $discount_value = (float) $tier['discount_percent'];
                } else {
                    continue; // Пропускаємо якщо немає значення
                }
            } else {
                if (isset($tier['discount_fixed']) && !empty($tier['discount_fixed'])) {
                    $discount_value = (float) $tier['discount_fixed'];
                } else {
                    continue; // Пропускаємо якщо немає значення
                }
            }

            $sales[] = new SalesBonus(
                (float) $tier['min_amount'],
                $discount_type,
                $discount_value
            );
        }

        // Сортуємо за мінімальною сумою
        usort($sales, function ($a, $b) {
            return $a->get_min_price() <=> $b->get_min_price();
        });

        return $sales;
    }

    public function discount()
    {
        if (is_null(WC()->cart)) {
            return;
        }

        $sales = $this->get_sales();
        $best_bonus = null;
        $subtotal = WC()->cart->get_subtotal();

        foreach ($sales as $sale) {
            if ($sale->is_available($subtotal)) {
                $current_discount = $sale->calculate_discount($subtotal);

                if ($best_bonus === null) {
                    $best_bonus = $sale;
                    continue;
                }

                $best_discount = $best_bonus->calculate_discount($subtotal);

                if ($current_discount > $best_discount) {
                    $best_bonus = $sale;
                }
            }
        }

        if ($best_bonus) {
            add_action('woocommerce_cart_calculate_fees', function ($cart) use ($best_bonus) {
                $best_bonus->add_bonus_discount($cart);
            });
        }
    }
}

new BonusSystem();