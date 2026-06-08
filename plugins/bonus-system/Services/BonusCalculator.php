<?php
namespace BonusSystem\Services;

class BonusCalculator
{
    public static function calculate_percent_discount(float $subtotal, float $percent): float
    {
        return $subtotal * ($percent / 100);
    }

    public static function calculate_fixed_discount(float $subtotal, float $fixedAmount): float
    {
        return min($fixedAmount, $subtotal);
    }

    public static function round_discound(float $discount, int $precision = 2): float
    {
        return round($discount, $precision);
    }
}