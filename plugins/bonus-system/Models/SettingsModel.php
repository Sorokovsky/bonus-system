<?php

namespace BonusSystem\Models;

class SettingsModel
{
    /**
     * @var array<SalesBonus>
     */
    private array $sales;

    /**
     * @var array<TextBonus>
     */
    private array $texts;

    private bool $applying_best;

    /**
     * Summary of __construct
     * @param array<SalesBonus> $sales
     * @param bool $applying_best
     * @param array<TextBonus> $texts
     */
    public function __construct(array $sales, bool $applying_best, array $texts)
    {
        $this->sales = $sales;
        $this->applying_best = $applying_best;
        $this->texts = $texts;
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


    /**
     * Summary of get_texts
     * @return array<TextBonus>
     */
    public function get_texts(): array
    {
        return $this->texts;
    }
}