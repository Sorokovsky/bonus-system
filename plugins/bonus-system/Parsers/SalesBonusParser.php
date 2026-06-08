<?php

namespace BonusSystem\Parsers;

use BonusSystem\Models\DiscountType;
use BonusSystem\Models\SalesBonus;

/**
 * @extends Parser<array<SalesBonus>>
 */
class SalesBonusParser implements Parser
{

    /**
     * @param array $settings
     * @return array<SalesBonus>
     */
    public function parse(array $settings): array
    {
        if (!isset($settings['sales']) || !is_array($settings['sales'])) {
            return [];
        }
        $sales_raw = $settings['sales'];
        $result = [];
        foreach ($sales_raw as $sale) {
            $discount_type = DiscountType::from($sale['discount_type']) ?? DiscountType::PERCENT;
            $discount_value = (float)($sale['discount_value'] ?? 10);
            $min_price = (float)($sale['min_price']) ?? 90;
            $result[] = new SalesBonus($min_price, $discount_type, $discount_value);
        }
        return $result;
    }
}