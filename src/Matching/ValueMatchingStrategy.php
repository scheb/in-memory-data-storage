<?php

namespace Scheb\InMemoryDataStorage\Matching;

class ValueMatchingStrategy implements ValueMatchingStrategyInterface
{
    /**
     * @var bool
     */
    private $useTypeSensitiveOperator;

    /**
     * @var array
     */
    private $customComparisonFunctions;

    /**
     * Basic implementation of a comparison strategy.
     *
     * @param bool  $useTypeSensitiveOperator   If the equals (==) or type-sensitive (===) operation should be used
     * @param array $customComparisonFunctions See addCustomComparisonFunction
     */
    public function __construct(bool $useTypeSensitiveOperator = true, array $customComparisonFunctions = [])
    {
        $this->useTypeSensitiveOperator = $useTypeSensitiveOperator;
        $this->customComparisonFunctions = $customComparisonFunctions;
    }

    public function setUseTypeSensitiveOperator(bool $useTypeSensitiveOperator): void
    {
        $this->useTypeSensitiveOperator = $useTypeSensitiveOperator;
    }

    /**
     * Add a custom comparison function, which takes two arguments and returns boolean if the values match.
     *
     * @param callable $callback
     */
    public function addCustomComparisonFunction(callable $callback): void
    {
        $this->customComparisonFunctions[] = $callback;
    }

    public function match($value1, $value2): bool
    {
        foreach ($this->customComparisonFunctions as $comparisonFunction) {
            if ($comparisonFunction($value1, $value2)) {
                return true;
            }
        }

        if ($this->useTypeSensitiveOperator) {
            return $value1 === $value2;
        }

        if ($this->isPrimitiveValue($value1) !== $this->isPrimitiveValue($value2)) {
            return false;
        }

        return $value1 == $value2;
    }

    private function isPrimitiveValue($value): bool
    {
        return is_null($value) || is_scalar($value);
    }
}
