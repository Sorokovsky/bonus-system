<?php

namespace BonusSystem\Models;

class SalesBonus implements Bonus
{
    private float $min_price;
    private DiscountType $discount_type;
    private float $discount_amount;

    public function __construct(float $min_price, DiscountType $discount_type, float $discount_amount)
    {
        $this->min_price = $min_price;
        $this->discount_type = $discount_type;
        $this->discount_amount = $discount_amount;
    }

    public function get_name(): string
    {
        return match ($this->discount_type) {
            DiscountType::PERCENT => sprintf(__("Бонусна знижка %s% (від %s ₴)", "bonus-system"), $this->discount_amount, $this->min_price),
            DiscountType::FIXED => sprintf(__("Бонусна знижка %s₴ (від %s ₴)", "bonus-system"), $this->discount_amount, $this->min_price),
        };
    }

    public function get_min_price(): float
    {
        return $this->min_price;
    }

    public function get_discount_amount(): float
    {
        return $this->discount_amount;
    }

    public function get_discount_type(): DiscountType
    {
        return $this->discount_type;
    }

    public function can_activate(): bool
    {
        if (!function_exists("WC") || !isset(WC()->cart)) {
            return false;
        }
        return WC()->cart->get_subtotal() >= $this->min_price;
    }

    public function activate(): void
    {
        add_action('woocommerce_cart_calculate_fees', function (\WC_Cart $cart) {
            $subtotal = $cart->get_subtotal();
            $discount = match ($this->discount_type) {
                DiscountType::FIXED => $this->discount_amount,
                DiscountType::PERCENT => $subtotal * ($this->discount_amount / 100)
            };
            if ($discount > 0) {
                $cart->add_fee($this->get_name(), -$discount);
            }
        }, 10, 1);
    }
}