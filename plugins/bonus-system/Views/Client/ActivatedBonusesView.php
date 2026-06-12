<?php
namespace BonusSystem\Views\Client;

use BonusSystem\Models\Bonus;

class ActivatedBonusesView
{
    /**
     * Summary of render
     * @param array<Bonus> $bonuses
     * @return string
     */
    public function render(array $bonuses): string
    {
        $html = '';
        $html .= <<<HTML
        <div class="bonuses">
            <h3>Застосовані бонуси</h3>
            <ul>
        HTML;
        foreach ($bonuses as $bonus) {
            $html .= <<<HTML
            <li>{$bonus->get_name()}</li>
            HTML;
        }
        $html .= <<<HTML
            </ul>
        </div>
        HTML;
        return $html;
    }
}