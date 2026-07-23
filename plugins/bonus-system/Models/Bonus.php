<?php

namespace BonusSystem\Models;

interface Bonus
{
    public function get_name(): string;

    public function can_activate(float $subtotal, int $products_count): bool;

    public function get_min_price(): float;

    public function get_min_products_count(): int;

    public function activate(): void;

    public function is_activated(): bool;
}