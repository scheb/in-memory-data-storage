<?php

namespace Scheb\InMemoryDataStorage\Matching;

class ValueMatcher implements ValueMatcherInterface
{
    /**
     * @var array
     */
    private $matchingStrategies;

    /**
     * @param bool                             $useTypeSensitiveOperator if the equals (==) or type-sensitive (===) operation should be used
     * @param ValueMatchingStrategyInterface[] $customMatchingStrategies
     */
    public function __construct(bool $useTypeSensitiveOperator = true, array $customMatchingStrategies = [])
    {
        $this->matchingStrategies = $customMatchingStrategies;
        if ($useTypeSensitiveOperator) {
            $this->matchingStrategies[] = new TypeSensitiveEqualsMatchingStrategy();
        } else {
            $this->matchingStrategies[] = new EqualsMatchingStrategy();
        }
    }

    public function match($value1, $value2): bool
    {
        foreach ($this->matchingStrategies as $matchingStrategy) {
            if ($matchingStrategy->match($value1, $value2)) {
                return true;
            }
        }

        return false;
    }
}
