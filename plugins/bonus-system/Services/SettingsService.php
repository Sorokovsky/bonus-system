<?php

namespace BonusSystem\Services;

use BonusSystem\Models\SettingsModel;
use BonusSystem\Parsers\ApplyingBestParser;
use BonusSystem\Parsers\SalesBonusParser;
use BonusSystem\Parsers\TextsParser;

class SettingsService
{
    public const OPTION_NAME = 'bonus_system_settings';

    private ApplyingBestParser $applying_best_parser;

    private SalesBonusParser $sales_bonus_parser;

    private TextsParser $texts_parser;

    public function __construct(ApplyingBestParser $applying_best_parser, SalesBonusParser $sales_bonus_parser, TextsParser $texts_parser)
    {
        $this->applying_best_parser = $applying_best_parser;
        $this->sales_bonus_parser = $sales_bonus_parser;
        $this->texts_parser = $texts_parser;
    }

    public function get_settings(): SettingsModel
    {
        $settings = get_option(self::OPTION_NAME, array());
        return new SettingsModel(
            $this->sales_bonus_parser->parse($settings),
            $this->applying_best_parser->parse($settings),
            $this->texts_parser->parse($settings)
        );
    }

    public function sanitize_settings(mixed $input): array
    {
        if (!is_array($input)) {
            return [];
        }
        $sanitized = [];
        if (isset($input['sales']) && is_array($input['sales'])) {
            foreach ($input['sales'] as $index => $sale) {
                $sanitized['sales'][$index]['min_price'] = floatval($sale['min_price'] ?? 0);
                $sanitized['sales'][$index]['discount_type'] = sanitize_text_field($sale['discount_type'] ?? 'percent');
                $sanitized['sales'][$index]['discount_value'] = floatval($sale['discount_value'] ?? 0);
                $sanitized['sales'][$index]['min_products_count'] = intval($sale['min_products_count'] ?? 0);
            }
        }

        if (isset($input['texts']) && is_array($input['texts'])) {
            foreach ($input['texts'] as $index => $text_bonus) {
                $sanitized['texts'][$index]['min_price'] = floatval($text_bonus['min_price']);
                $sanitized['texts'][$index]['name'] = sanitize_text_field($text_bonus['name']);
                $sanitized['texts'][$index]['name'] = sanitize_text_field($text_bonus['name']);
                $sanitized['texts'][$index]['min_products_count'] = intval($text_bonus['min_products_count']);
            }
        }

        return $sanitized;
    }
}