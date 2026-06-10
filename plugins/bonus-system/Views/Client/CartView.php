<?php
namespace BonusSystem\Views\Client;

use BonusSystem\Models\Bonus;

class CartView
{

    private ?Bonus $next;

    /**
     * Summary of render
     * @param array<Bonus> $all_bonuses
     * @param array<Bonus> $activated_bonuses
     */
    public function render(array $all_bonuses, array $activated_bonuses): string
    {
        $subtotal = WC()->cart->get_subtotal();
        $all_count = count($all_bonuses);
        $activated_count = count($activated_bonuses);
        $next_text = 'Всі бонуси активавано';
        $differcence = $this->get_difference($all_bonuses, $activated_bonuses);
        $percent = $this->get_percent();
        if ($all_count !== $activated_count) {
            $next_text = 'Для отримання "' . $this->next->get_name() . '" доберіть товарів, ще на ' . $differcence . ' грн';
        }
        $html = '';
        $html .= <<<HTML
        <div class="bonus-cart-container">
            <h2>{$next_text}</h2>
            <div class='range'>
                <span class='range-body' style='width: {$percent}%'></span>
            </div>
            <div class='bonuses'>
                <h3>Застосовані бонуси</h3>
                <ul>
HTML;
        foreach ($activated_bonuses as $bonus) {
            $html .= <<<HTML
                            <li>{$bonus->get_name()}</li>
                        HTML;
        }
        $html .= <<<HTML
                </ul>
            </div>
        </div>
        <style>
            .range {
                height: 10px;
                background: black;
                position: relative;
                z-index: 0;
            }
            .range-body {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                display: block;
                background: green;
            }
        </style>
        HTML;
        return $html;
    }

    /**
     * Summary of get_difference
     * @param array<Bonus> $all
     * @param array<Bonus> $activated
     * @return float
     */
    private function get_difference(array $all, array $activated): float
    {
        if (count($activated) == 0) {
            return $all[0]->get_min_price();
        }
        $last_activated = $activated[count($activated) - 1];
        $this->next = null;
        foreach ($all as $index => $bonus) {
            if (isset($all[$index + 1])) {
                $this->next = $all[$index + 1];
            }
            break;
        }
        if ($this->next === null) {
            return 0;
        }
        return $this->next->get_min_price() - $last_activated->get_min_price();
    }

    private function get_percent(): float
    {
        if ($this->next === null) {
            return 100;
        }
        $subtotal = WC()->cart->get_subtotal();
        $bonus_price = $this->next->get_min_price();
        return min(($subtotal * 100) / $bonus_price, 100);
    }
}