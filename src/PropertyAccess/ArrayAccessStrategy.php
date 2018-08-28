<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

class ArrayAccessStrategy implements PropertyAccessStrategyInterface
{
    public function supports($valueObject): bool
    {
        return is_array($valueObject) || $valueObject instanceof \ArrayAccess;
    }

    public function getPropertyValue($valueObject, string $propertyName)
    {
        if (!$this->supports($valueObject)) {
            throw new \InvalidArgumentException('$valueObject must be array or instance of \\ArrayAccess.');
        }

        return $valueObject[$propertyName] ?? null;
    }

    public function setPropertyValue(&$valueObject, string $propertyName, $value): bool
    {
        // Setting values on arrays is not supported, since they're not passed by reference so the change will be lost.
        return false;
    }
}
