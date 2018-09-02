<?php

namespace Scheb\InMemoryDataStorage\Matching;

use Scheb\Comparator\ComparatorInterface;

class ValueMatcher implements ValueMatcherInterface
{
    /**
     * @var ComparatorInterface
     */
    private $comparator;

    public function __construct(ComparatorInterface $comparator)
    {
        $this->comparator = $comparator;
    }

    public function match($value1, $value2): bool
    {
        return $this->comparator->isEqual($value1, $value2);
    }
}
