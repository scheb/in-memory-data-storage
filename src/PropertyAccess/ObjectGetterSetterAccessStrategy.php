<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

class ObjectGetterSetterAccessStrategy implements PropertyAccessStrategyInterface
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

        $getterName = 'get'.ucfirst($propertyName);
        if ($this->hasPublicMethod($valueObject, $getterName)) {
            return $valueObject->{$getterName}();
        }

        $boolGetterName = 'is'.ucfirst($propertyName);
        if ($this->hasPublicMethod($valueObject, $boolGetterName)) {
            return $valueObject->{$boolGetterName}();
        }

        return null;
    }

    public function setPropertyValue(&$valueObject, string $propertyName, $value): bool
    {
        if (!$this->supports($valueObject)) {
            throw new \InvalidArgumentException('$valueObject must be an object.');
        }

        $setterName = 'set'.ucfirst($propertyName);
        if ($this->hasPublicMethod($valueObject, $setterName)) {
            $valueObject->{$setterName}($value);

            return true;
        }

        return false;
    }

    private function hasPublicMethod($valueObject, string $methodName): bool
    {
        return is_callable([$valueObject, $methodName]);
    }
}
