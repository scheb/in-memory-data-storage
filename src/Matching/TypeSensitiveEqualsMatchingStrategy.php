<?php

namespace Scheb\InMemoryDataStorage\Matching;

class TypeSensitiveEqualsMatchingStrategy implements ValueMatchingStrategyInterface
{
    public function match($value1, $value2): bool
    {
        return $value1 === $value2;
    }
}
