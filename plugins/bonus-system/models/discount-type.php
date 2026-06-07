<?php
namespace Bonuses\Models;
enum DiscountType: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
}