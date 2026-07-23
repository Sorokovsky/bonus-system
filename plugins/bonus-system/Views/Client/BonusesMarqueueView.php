<?php
namespace BonusSystem\Views\Client;

use BonusSystem\Models\Bonus;

class BonusesMarqueueView
{
    /**
     * Summary of render
     * @param array<Bonus> $bonuses
     * @return string
     */
    public function render(array $bonuses): string
    {
        $html = <<<HTML
        <section class="marqueue">
            <div class="maqueue-container">
                <span class="marqueue-arrow marqueue-prev"><</span>
                <ul>
        HTML;
        foreach ($bonuses as $bonus) {
            $text = $bonus->get_name() . ' від ' . $bonus->get_min_price() . ' грн';
            $html .= <<<HTML
                    <li>{$text}</li>
                    HTML;
        }
        $html .= <<<HTML
                </ul>
                <span class="marqueue-arrow marqueue-next">></span>
            </div>
        </section>
        HTML;
        return $html;
    }
}