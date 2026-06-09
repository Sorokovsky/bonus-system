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

    public function cart_page(): void
    {
        $this->view->render($this->service->get_all());
        exit;
    }
}