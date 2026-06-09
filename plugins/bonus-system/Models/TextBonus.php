<?php
namespace BonusSystem\Models;

use Override;

class TextBonus implements Bonus
{
    private string $name;

    private float $min_price;

    public function __construct(string $name, float $min_price)
    {
        $this->name = $name;
        $this->min_price = $min_price;
    }

    public function get_min_price(): float
    {
        return $this->min_price;
    }

    #[Override]
    public function get_name(): string
    {
        return $this->name;
    }

    #[Override]
    public function activate(): void
    {

    }

    #[Override]
    public function can_activate(): bool
    {
        if (!function_exists("WC") || WC()->cart === null) {
            return false;
        }
        return WC()->cart->get_subtotal() >= $this->min_price;
    }
}