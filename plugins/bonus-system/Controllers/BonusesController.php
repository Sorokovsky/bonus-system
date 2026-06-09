<?php

namespace BonusSystem\Controllers;

use BonusSystem\Services\BonusesService;

class BonusesController
{
    private BonusesService $service;

    public function __construct(BonusesService $service)
    {
        $this->service = $service;
    }

    public function apply(): void
    {
        $this->service->apply();
    }
}