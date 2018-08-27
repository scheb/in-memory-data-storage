<?php

namespace Scheb\InMemoryDataStorage\Matching;

interface ValueMatchingStrategyInterface
{
    /**
     * Compare two values if they're matching.
     *
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return bool
     */
    public function match($value1, $value2): bool;
}
