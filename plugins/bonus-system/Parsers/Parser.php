<?php

namespace BonusSystem\Parsers;

/**
 * @template T
 */
interface Parser
{
    /**
     * @return T
     */
    public function parse(array $settings);
}