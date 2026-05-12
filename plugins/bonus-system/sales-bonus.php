<?php
class SalesBonus
{
    private float $min_price;
    private string $discount_type;
    private float $discount_value;
    private float $current_discount = 0;

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

    public function get_discount_sum(): float
    {
        return $this->current_discount;
    }

    public function is_available($subtotal = null)
    {
        if ($subtotal === null && !is_null(WC()->cart)) {
            $subtotal = WC()->cart->get_subtotal();
        }

        if ($subtotal === null) {
            return false;
        }

        return $subtotal >= $this->min_price;
    }

    public function calculate_discount($subtotal): float
    {
        if ($this->discount_type === 'percent') {
            return $subtotal * ($this->discount_value / 100);
        } else {
            return min($this->discount_value, $subtotal);
        }
    }

    public function add_bonus_discount($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        $subtotal = $cart->get_subtotal();
        $discount = $this->calculate_discount($subtotal);

        if ($discount > 0) {
            $label = $this->discount_type === 'percent'
                ? sprintf(__('Бонусна знижка %s%% (від %s ₴)', 'bonus-system'), $this->discount_value, $this->min_price)
                : sprintf(__('Бонусна знижка %s ₴ (від %s ₴)', 'bonus-system'), $this->discount_value, $this->min_price);

            $cart->add_fee($label, -$discount);
        }
    }
}