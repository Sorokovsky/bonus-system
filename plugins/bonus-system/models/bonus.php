<?php

namespace Bonuses\Models;

interface Bonus
{
    public function get_name(): string;

    public function execute(): void;

    public function can_execute(): bool;
}