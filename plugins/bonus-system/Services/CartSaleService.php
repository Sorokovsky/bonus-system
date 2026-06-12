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
            $price = (float) $product->get_price();
            $regular_price = (float) $product->get_regular_price();
            if ($price !== $regular_price) {
                return true;
            }
        }
        return false;
    }
}