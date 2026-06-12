<?php
namespace BonusSystem\Views\Client;

use BonusSystem\Models\Bonus;

class BonusProgresView
{
    /**
     * Summary of render
     * @param Bonus $next_bonus
     * @param float $difference
     * @return string
     */
    public function render(?Bonus $next_bonus, float $difference, float $percent): string
    {
        $next_text = 'Вітаємо! Ви досягнули усіх можливих бонусів';
        if ($next_bonus !== null) {
            $next_text = 'Щоб скористатися бонусом "' . $next_bonus->get_name() . '" доберіть товарів ще на ' . $difference . ' грн';
        }
        $html = '';
        $html .= <<<HTML
        <div class="bonus-cart-container">
            <h2>{$next_text}</h2>
        </div>
        <div class='range'>
                <span class='range-body' style='width: {$percent}%'></span>
            </div>
HTML;
        return $html;
    }
}