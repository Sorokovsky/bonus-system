<?php
function get_total_price()
{
    return WC()->cart->get_subtotal();
}