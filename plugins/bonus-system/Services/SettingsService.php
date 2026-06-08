<?php

namespace BonusSystem\Services;

use BonusSystem\Models\SettingsModel;
use BonusSystem\Parsers\ApplyingBestParser;
use BonusSystem\Parsers\SalesBonusParser;

class SettingsService
{
    const OPTION_NAME = 'bonus_system_settings';

    private ApplyingBestParser $applying_best_parser;

    private SalesBonusParser $sales_bonus_parser;

    public function __construct($applying_best_parser, $sales_bonus_parser)
    {
        $this->applying_best_parser = $applying_best_parser;
        $this->sales_bonus_parser = $sales_bonus_parser;
    }

    public function get_settings(): SettingsModel
    {
        $settings = get_option(self::OPTION_NAME, array());
        return new SettingsModel($this->sales_bonus_parser->parse($settings), $this->applying_best_parser->parse($settings));
    }
}