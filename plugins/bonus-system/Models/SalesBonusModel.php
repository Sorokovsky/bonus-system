<?php
namespace BonusSystem\Models;

class SalesBonusModel
{
    private float $min_price;
    private string $discount_type;
    private float $discount_value;
    private ?float $current_discount = null;

    public function __construct(float $min_price, string $discount_type, float $discount_value)
    {
        $this->min_price = $min_price;
        $this->discount_type = $discount_type;
        $this->discount_value = $discount_value;
    }

    public function get_min_price(): float
    {
        return $this->min_price;
    }

    public function get_discount_type(): string
    {
        return $this->discount_type;
    }

    public function get_discount_value(): float
    {
        return $this->discount_value;
    }

    public function is_available(?float $subtotal = null): bool
    {
        if ($subtotal === null && function_exists('WC')) {
            $subtotal = WC()->cart->get_subtotal();
        }
        return $subtotal !== null && $subtotal >= $this->min_price;
    }

    public function calculate_discount(float $subtotal): float
    {
        $discount = 0;
        if ($this->discount_type === 'percent') {
            $discount = $subtotal * ($this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discount = min($this->discount_value, $subtotal);
        }

        $this->current_discount = $discount;
        return $discount;
    }

    public function get_discount_label(): string
    {
        if ($this->discount_type === 'percent') {
            return sprintf(
                __('Бонусна знижка %s%% (від %s ₴)', 'bonus-system'),
                $this->discount_value,
                $this->min_price
            );
        }

        return sprintf(
            __('Бонусна знижка %s ₴ (від %s ₴)', 'bonus-system'),
            $this->discount_value,
            $this->min_price
        );
    }
}