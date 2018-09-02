<?php

namespace Scheb\InMemoryDataStorage;

use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;

class Comparison
{
    public static function notNull(): callable
    {
        return function ($propertyValue) {
            return null !== $propertyValue;
        };
    }

    public static function equals($referenceValue): callable
    {
        return function ($propertyValue, ValueMatcherInterface $valueMatcher) use ($referenceValue) {
            return $valueMatcher->match($propertyValue, $referenceValue);
        };
    }

    public static function greaterThan(float $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!is_numeric($propertyValue)) {
                return false;
            }

            return $propertyValue > $referenceValue;
        };
    }

    public static function greaterThanOrEqual(float $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!is_numeric($propertyValue)) {
                return false;
            }

            return $propertyValue >= $referenceValue;
        };
    }

    public static function lessThan(float $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!is_numeric($propertyValue)) {
                return false;
            }

            return $propertyValue < $referenceValue;
        };
    }

    public static function lessThanOrEqual(float $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!is_numeric($propertyValue)) {
                return false;
            }

            return $propertyValue <= $referenceValue;
        };
    }

    public static function between(float $min, float $max): callable
    {
        return function ($propertyValue) use ($min, $max) {
            if (!is_numeric($propertyValue)) {
                return false;
            }

            return $propertyValue >= $min && $propertyValue <= $max;
        };
    }

    public static function dateGreaterThan(\DateTimeInterface $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!$propertyValue instanceof \DateTimeInterface) {
                return false;
            }

            return $propertyValue > $referenceValue;
        };
    }

    public static function dateGreaterThanOrEqual(\DateTimeInterface $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!$propertyValue instanceof \DateTimeInterface) {
                return false;
            }

            return $propertyValue >= $referenceValue;
        };
    }

    public static function dateLessThan(\DateTimeInterface $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!$propertyValue instanceof \DateTimeInterface) {
                return false;
            }

            return $propertyValue < $referenceValue;
        };
    }

    public static function dateLessThanOrEqual(\DateTimeInterface $referenceValue): callable
    {
        return function ($propertyValue) use ($referenceValue) {
            if (!$propertyValue instanceof \DateTimeInterface) {
                return false;
            }

            return $propertyValue <= $referenceValue;
        };
    }

    public static function dateBetween(\DateTimeInterface $min, \DateTimeInterface $max): callable
    {
        return function ($propertyValue) use ($min, $max) {
            if (!$propertyValue instanceof \DateTimeInterface) {
                return false;
            }

            return $propertyValue >= $min && $propertyValue <= $max;
        };
    }

    public static function inArray(array $givenValues): callable
    {
        return function ($propertyValue, ValueMatcherInterface $valueMatcher) use ($givenValues) {
            foreach ($givenValues as $givenValue) {
                if ($valueMatcher->match($propertyValue, $givenValue)) {
                    return true;
                }
            }

            return false;
        };
    }

    public function arrayElementEquals($referenceValue)
    {
        return function ($propertyValue, ValueMatcherInterface $valueMatcher) use ($referenceValue) {
            if (is_iterable($propertyValue)) {
                foreach ($propertyValue as $arrayItem) {
                    if ($valueMatcher->match($arrayItem, $referenceValue)) {
                        return true;
                    }
                }
            }

            return false;
        };
    }
}
