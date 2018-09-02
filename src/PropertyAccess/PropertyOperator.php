<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;
use Scheb\PropertyAccess\PropertyAccessInterface;

class PropertyOperator implements PropertyOperatorInterface
{
    /**
     * @var ValueMatcherInterface
     */
    private $valueMatcher;

    /**
     * @var PropertyAccessInterface
     */
    private $propertyAccess;

    public function __construct(ValueMatcherInterface $valueMatcher, PropertyAccessInterface $propertyAccess)
    {
        $this->valueMatcher = $valueMatcher;
        $this->propertyAccess = $propertyAccess;
    }

    public function getPropertyValue($item, string $propertyName)
    {
        return $this->propertyAccess->getPropertyValue($item, $propertyName);
    }

    public function setPropertyValue($item, string $propertyName, $newValue)
    {
        return $this->propertyAccess->setPropertyValue($item, $propertyName, $newValue);
    }

    public function getItemsWithMatchingCriteria(iterable $items, array $criteria): \Traversable
    {
        foreach ($items as $item) {
            if ($this->matchCriteria($item, $criteria)) {
                yield $item;
            }
        }
    }

    private function matchCriteria($item, array $criteria): bool
    {
        foreach ($criteria as $propertyName => $expectedValue) {
            if (!$this->matchPropertyValue($item, $propertyName, $expectedValue)) {
                return false;
            }
        }

        return true;
    }

    private function matchPropertyValue($item, string $propertyName, $comparison): bool
    {
        $propertyValue = $this->propertyAccess->getPropertyValue($item, $propertyName);
        if (is_callable($comparison)) {
            return $comparison($propertyValue, $this->valueMatcher);
        }

        return $this->valueMatcher->match($propertyValue, $comparison);
    }
}
