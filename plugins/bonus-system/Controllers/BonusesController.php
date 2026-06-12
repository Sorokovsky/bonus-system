<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\BonusesService;
use BonusSystem\Views\Client\ActivatedBonusesView;
use BonusSystem\Views\Client\BonusProgresView;

class BonusesController
{
    private BonusesService $service;
    private BonusProgresView $progres_view;
    private ActivatedBonusesView $activated_bonuses_view;

    public function __construct(
        BonusesService $service,
        BonusProgresView $progres_view,
        ActivatedBonusesView $activated_bonuses_view
    ) {
        $this->service = $service;
        $this->progres_view = $progres_view;
        $this->activated_bonuses_view = $activated_bonuses_view;
    }

    public function apply(): void
    {
        $this->service->apply();
    }

    public function cart_page(): string
    {
        return $this->progres_view->render(
            $this->service->get_next_bonus(),
            $this->service->get_difference(),
            $this->service->get_percent()
        ) . $this->activated_bonuses_view->render($this->service->get_activated_bonuses());
    }

    public function save_bonuses(int $order_id, $order): void
    {
        $activated = $this->service->get_activated_bonuses();
        delete_post_meta($order_id, '_applied_bonuses');

        if (empty($activated)) {
            return;
        }

        $bonuses_data = [];
        foreach ($activated as $bonus) {
            $bonuses_data[] = $bonus->get_name();
        }

        update_post_meta($order_id, '_applied_bonuses', implode("; ", $bonuses_data));
    }
}