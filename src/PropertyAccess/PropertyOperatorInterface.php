<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

interface PropertyOperatorInterface
{
    /**
     * Get a property from an item.
     *
     * @param mixed  $valueObject
     * @param string $propertyName
     *
     * @return mixed|null
     */
    public function getPropertyValue($valueObject, string $propertyName);

    /**
     * Set the property on an item.
     *
     * @param mixed  $valueObject
     * @param string $propertyName
     * @param mixed  $value
     *
     * @return mixed The modified object
     */
    public function setPropertyValue($valueObject, string $propertyName, $value);

    /**
     * Return an iterator with all items matching the criteria.
     *
     * @param array $items
     * @param array $criteria
     *
     * @return \Traversable
     */
    public function getItemsWithMatchingCriteria(array $items, array $criteria): \Traversable;
}
