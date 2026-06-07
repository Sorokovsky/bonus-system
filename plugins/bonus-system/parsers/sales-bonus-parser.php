<?php
namespace Bonuses\Parsers;

use Override;
use Bonuses\Models;

require_once './parser.php';
require_once '../models/sales-bonus.php';
require_once '../models/discount-type.php';

/**
 * @implements Parser<Bonus>
 */
class SalesBonusParser implements Parser
{
    public function __construct()
    {
    }

    #[Override]
    public function parse(array $parameters): object
    {
        $min_price = (float) $parameters['min_amount'];
        $type = (string) $parameters['type'];
        $value = (float) $parameters['value'];
        return new SalesBonus($min_price, DiscountType::from($type), $value);
    }
}