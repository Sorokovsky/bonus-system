<?php
namespace Bonuses\Parsers;

/**
 * @template T
 */
interface Parser
{
    /**
     * @return T
     */
    public function parse(array $parameters): object;
}