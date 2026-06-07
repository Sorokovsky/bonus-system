<?php
namespace Bonuses\Models;

use Override;

require_once "./bonus.php";
require_once "./discount-type.php";

class SalesBonus implements Bonus
{
    final private float $min_price;

    final private DiscountType $discount_type;

    final private float $discount_value;

    public function __construct(float $min_price, DiscountType $discount_type, float $discount_value)
    {
        $this->min_price = $min_price;
        $this->discount_type = $discount_type;
        $this->discount_value = $discount_value;
    }

    #[Override]
    public function get_name(): string
    {
        return match ($this->discount_type) {
            DiscountType::PERCENT => sprintf(__('Бонусна знижка %s%% (від %s ₴)', 'bonus-system'), $this->discount_value, $this->min_price),
            DiscountType::FIXED => sprintf(__('Бонусна знижка %s ₴ (від %s ₴)', 'bonus-system'), $this->discount_value, $this->min_price),
            default => __('Невідомо', 'bonus-system')
        };
    }

    #[Override]
    public function execute(): void
    {
        $discount = $this->calculate_discount(WC()->cart->get_subtotal());
        WC()->cart->add_fee($this->get_name(), -$discount);
    }

    #[Override]
    public function can_execute(): bool
    {
        return WC()->cart->get_subtotal() >= $this->min_price;
    }

    private function calculate_discount(float $price): float
    {
        return match ($this->discount_type) {
            DiscountType::FIXED => $this->discount_value,
            DiscountType::PERCENT => $price * ($this->discount_value / 100),
            default => 0
        };
    }
}