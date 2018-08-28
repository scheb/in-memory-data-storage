<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

class ObjectPropertyAccessStrategy implements PropertyAccessStrategyInterface
{
    public function supports($valueObject): bool
    {
        return is_object($valueObject);
    }

    public function getPropertyValue($valueObject, string $propertyName)
    {
        if (!$this->supports($valueObject)) {
            throw new \InvalidArgumentException('$valueObject must be an object.');
        }

        $properties = get_object_vars($valueObject);

        return $properties[$propertyName] ?? null;
    }

    public function setPropertyValue(&$valueObject, string $propertyName, $value): bool
    {
        if (!$this->supports($valueObject)) {
            throw new \InvalidArgumentException('$valueObject must be an object.');
        }

        $properties = get_object_vars($valueObject);
        if (in_array($propertyName, array_keys($properties))) {
            $valueObject->{$propertyName} = $value;

            return true;
        }

        return false;
    }
}
