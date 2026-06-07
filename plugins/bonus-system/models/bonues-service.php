<?php
namespace Bonuses\Models;

require_once './sales-bonus.php';
require_once './bonus.php';
require_once './discount-type.php';
require_once '../parsers/sales-bonus.parser.php';

use \Bonuses\Parsers;
use Bonuses\Parsers\SalesBonusParser;

class BonusesService
{
    final private SalesBonusParser $sales_parser;

    public function __construct()
    {
        $this->sales_parser = new SalesBonusParser();
    }

    /**
     * @return array<Bonus>
     */
    public function get_bonuses(): array
    {
        $result = [];
        $tiers = get_option("bonus_system_sales_bonuses", []);
        if (!empty($tiers)) {
            foreach ($tiers as $tier) {

                $result[] = $this->sales_parser->parse($tier);
            }
        }
        return $result;
    }
}