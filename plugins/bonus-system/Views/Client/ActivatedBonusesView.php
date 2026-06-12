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
        $title = 'Застосовані бонуси';
        if (count($bonuses) === 0) {
            $title = 'Не має застосованих бонусів';
        }
        $html = '';
        $html .= <<<HTML
        <div class="bonuses">
            <h3>{$title}</h3>
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