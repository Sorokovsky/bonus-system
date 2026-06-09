<?php

namespace BonusSystem\Models;

interface Bonus
{
    public function get_name(): string;

    public function can_activate(): bool;

    public function get_min_price(): float;

    public function activate(): void;

    public function is_activated(): bool;
}