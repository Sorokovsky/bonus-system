<?php
namespace BonusSystem\Parsers;

use BonusSystem\Models\TextBonus;
use Override;

/**
 * Summary of TextsParse
 * @implements Parser<array<TextBonus>>
 */
class TextsParser implements Parser
{
    public function __construct()
    {
    }

    /**
     * @return array<TextBonus>
     */
    #[Override]
    public function parse(array $settings)
    {
        if (!isset($settings['texts']) || !is_array($settings['texts'])) {
            return [];
        }
        $texts_raw = $settings['texts'];
        $result = [];
        foreach ($texts_raw as $text_raw) {
            $min_price = (float) ($text_raw['min_price'] ?? 0);
            $name = $text_raw['name'] ?? '';
            $result[] = new TextBonus($name, $min_price);
        }
        return $result;
    }
}