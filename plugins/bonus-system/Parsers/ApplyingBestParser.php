<?php
namespace BonusSystem\Parsers;

/**
 * @extends Parser<boolean>
 */
class ApplyingBestParser implements Parser
{
    /**
     * @param array $settings
     * @return boolean
     */
    public function parse(array $settings): bool
    {
        return $settings['apply_best'] ?? false;
    }
}