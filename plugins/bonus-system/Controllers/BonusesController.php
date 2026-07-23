<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\BonusesService;
use BonusSystem\Views\Client\ActivatedBonusesView;
use BonusSystem\Views\Client\BonusesMarqueueView;
use BonusSystem\Views\Client\BonusProgresView;
use BonusSystem\Models\SalesBonus;

class BonusesController
{
    private BonusesService $service;

    private BonusProgresView $progres_view;
    private ActivatedBonusesView $activated_bonuses_view;
    private BonusesMarqueueView $bonuses_marqueue_view;

    public function __construct(
        BonusesService $service,
        BonusProgresView $progres_view,
        ActivatedBonusesView $activated_bonuses_view,
        BonusesMarqueueView $bonuses_marqueue_view
    ) {
        $this->service = $service;
        $this->progres_view = $progres_view;
        $this->activated_bonuses_view = $activated_bonuses_view;
        $this->bonuses_marqueue_view = $bonuses_marqueue_view;
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
            $this->service->get_percent(),
            $this->service->has_sale_product()
        ) . $this->activated_bonuses_view->render($this->service->get_activated_bonuses(), $this->service->get_products_count(), $this->service->get_next_bonus());
    }

    public function save_bonuses(int $order_id): void
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        $activated = $this->service->get_activated_bonuses();
        delete_post_meta($order_id, 'applied_bonuses');

        if (empty($activated)) {
            return;
        }

        $bonuses_data = [];
        $discount = 2;
        foreach ($activated as $bonus) {
            $bonuses_data[] = $bonus->get_name();
            if ($bonus instanceof SalesBonus) {
                $discount = $bonus->get_calculated();
            }
        }

        $order->update_meta_data('applied_discount', $discount);
        $order->update_meta_data('applied_bonuses', implode(", ", $bonuses_data));
        $order->save();
    }

    public function show_bonuses($order): void
    {
        $bonuses = $order->get_meta('applied_bonuses', true);
        if ($bonuses) {
            echo '<p><strong> Застосовані бонуси: ' . $bonuses . '</strong></p>';
        }
    }

    public function bonuses_marqueue(): string
    {
        return $this->bonuses_marqueue_view->render($this->service->get_all());
    }
}