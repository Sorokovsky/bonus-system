<?php
namespace BonusSystem\Models;

use Override;

class TextBonus implements Bonus
{
    private string $name;

    private float $min_price;
    private int $min_products_count;
    private bool $activated;

    public function __construct(string $name, float $min_price, int $min_products_count)
    {
        $this->name = $name;
        $this->min_price = $min_price;
        $this->activated = false;
        $this->min_products_count = $min_products_count;
    }

    #[Override]
    public function is_activated(): bool
    {
        return $this->activated;
    }

    #[Override]
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
        $this->activated = true;
    }

    #[Override]
    public function get_min_products_count(): int
    {
        return $this->min_products_count;
    }

    #[Override]
    public function can_activate(float $subtotal, int $products_count): bool
    {
        return $subtotal >= $this->min_price && $products_count >= $this->min_products_count;
    }
}