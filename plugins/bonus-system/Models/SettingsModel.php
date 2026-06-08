<?php

namespace BonusSystem\Models;

class SettingsModel
{
    /**
     * @var array<SalesBonus>
     */
    private array $sales;

    private bool $applying_best;

    public function __construct(array $sales, bool $applying_best) {
        $this->sales = $sales;
        $this->applying_best = $applying_best;
    }

    /**
     * @return array<SalesBonus>
     */
    public function get_sales(): array
    {
        return $this->sales;
    }

    public function apply_best(): bool
    {
        return $this->applying_best;
    }
}