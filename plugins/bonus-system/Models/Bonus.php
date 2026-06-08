<?php

namespace BonusSystem\Models;

interface Bonus
{
    public function get_name(): string;

    public function can_activate(): bool;

    public function activate(): void;
}