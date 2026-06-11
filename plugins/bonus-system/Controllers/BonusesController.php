<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\BonusesService;
use BonusSystem\Views\Client\CartView;

class BonusesController
{
    private BonusesService $service;
    private CartView $view;

    public function __construct(BonusesService $service, CartView $view)
    {
        $this->service = $service;
        $this->view = $view;
    }

    public function apply(): void
    {
        $this->service->apply();
    }

    public function cart_page(): string
    {
        return $this->view->render($this->service->get_all(), $this->service->get_activated_bonuses());
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