<?php

namespace BonusSystem\Models;

use Override;

class SalesBonus implements Bonus
{
    private float $min_price;
    private int $min_products_count;
    private DiscountType $discount_type;
    private float $discount_amount;
    private bool $activated;
    private float $calculated;

    public function __construct(float $min_price, DiscountType $discount_type, float $discount_amount, int $min_products_count)
    {
        $this->min_price = $min_price;
        $this->discount_type = $discount_type;
        $this->discount_amount = $discount_amount;
        $this->activated = false;
        $this->calculated = 0;
        $this->min_products_count = $min_products_count;
    }

    public function get_name(): string
    {
        return match ($this->discount_type) {
            DiscountType::PERCENT => sprintf(__("Знижка %s%%", "bonus-system"), $this->discount_amount),
            DiscountType::FIXED => sprintf(__("Знижка %s₴", "bonus-system"), $this->discount_amount),
        };
    }

    #[Override]
    public function is_activated(): bool
    {
        return $this->activated;
    }

    #[Override]
    public function get_min_products_count(): int
    {
        return $this->min_products_count;
    }

    #[Override]
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

    public function can_activate(float $subtotal, int $products_count): bool
    {
        return round($subtotal) >= round($this->min_price) && $products_count >= $this->min_products_count;
    }

    public function activate(): void
    {
        add_action('woocommerce_cart_calculate_fees', function (\WC_Cart $cart) {
            $subtotal = $cart->get_subtotal();
            $discount = $this->calculate($subtotal);
            $this->calculated = $discount;
            if ($discount > 0) {
                $cart->add_fee($this->get_name(), -$discount);
                $this->activated = true;
            }
        }, 10, 1);
    }

    public function get_calculated(): float
    {
        return $this->calculated;
    }

    private function calculate(float $subtotal): float
    {
        return match ($this->discount_type) {
            DiscountType::FIXED => $this->discount_amount,
            DiscountType::PERCENT => $subtotal * ($this->discount_amount / 100)
        };
    }
}