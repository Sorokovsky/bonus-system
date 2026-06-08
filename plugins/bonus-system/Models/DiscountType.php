<?php

namespace BonusSystem\Models;

enum DiscountType : string
{
    case PERCENT = "percent";
    case FIXED = "fixed";
}