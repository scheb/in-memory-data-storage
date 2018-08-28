<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

use Scheb\InMemoryDataStorage\Exception\SetPropertyFailedException;

class PropertyAccess
{
    /**
     * @var PropertyAccessStrategyInterface[]
     */
    private $propertyAccessStrategies;

    /**
     * @param PropertyAccessStrategyInterface[] $propertyAccessStrategies
     */
    public function __construct(array $propertyAccessStrategies)
    {
        $this->propertyAccessStrategies = $propertyAccessStrategies;
    }

    public function getPropertyValue($valueObject, string $propertyName)
    {
        foreach ($this->propertyAccessStrategies as $propertyAccessStrategy) {
            if ($propertyAccessStrategy->supports($valueObject)) {
                $value = $propertyAccessStrategy->getPropertyValue($valueObject, $propertyName);
                if (null !== $value) {
                    return $value;
                }
            }
        }

        return null;
    }

    public function setPropertyValue(&$valueObject, string $propertyName, $value): void
    {
        foreach ($this->propertyAccessStrategies as $propertyAccessStrategy) {
            if ($propertyAccessStrategy->supports($valueObject)) {
                if ($propertyAccessStrategy->setPropertyValue($valueObject, $propertyName, $value)) {
                    return;
                }
            }
        }

        throw new SetPropertyFailedException('Property "'.$propertyName.'" could not be set.');
    }
}
