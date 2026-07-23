<?php
namespace BonusSystem\Services;

class CartSaleService
{
    public function __construct()
    {

    }

    public function has_sale_product(): bool
    {
        $cart = WC()->cart->get_cart();
        foreach ($cart as $order) {
            $product = $order['data'];
            if ($this->is_sale_product($product)) {
                return true;
            }
        }
        return false;
    }

    public function get_total_price(): float
    {
        $subtotal = WC()->cart->get_subtotal();
        foreach (WC()->cart->get_cart() as $order) {
            $product = $order['data'];
            if ($this->is_sale_product($product)) {
                $subtotal -= (float)$order['line_total'];
            }
        }
        return $subtotal;
    }

    private function is_sale_product(mixed $product): bool
    {
        $price = (float) $product->get_price();
        $regular_price = (float) $product->get_regular_price();
        return $price !== $regular_price;
    }
}