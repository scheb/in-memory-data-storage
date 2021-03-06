<?php

namespace Scheb\InMemoryDataStorage;

use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;
use Scheb\InMemoryDataStorage\Exception\NamedItemNotFoundException;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperatorInterface;

class DataRepository
{
    public const OPTION_STRICT_GET = 1;
    public const OPTION_STRICT_UPDATE = 2;
    public const OPTION_STRICT_REMOVE = 4;

    public const SORT_ORDER_ASC = 1;
    public const SORT_ORDER_DESC = -1;

    /**
     * @var DataStorageInterface
     */
    private $dataStorage;

    /**
     * @var PropertyOperatorInterface
     */
    private $propertyOperator;

    /**
     * @var bool
     */
    private $strictGet;

    /**
     * @var bool
     */
    private $strictUpdate;

    /**
     * @var bool
     */
    private $strictRemove;

    public function __construct(DataStorageInterface $dataStorage, PropertyOperatorInterface $propertyOperator, int $options = 0)
    {
        $this->dataStorage = $dataStorage;
        $this->propertyOperator = $propertyOperator;
        $this->setOptions($options);
    }

    public function setOptions(int $options): void
    {
        $this->strictGet = (bool) ($options & self::OPTION_STRICT_GET);
        $this->strictUpdate = (bool) ($options & self::OPTION_STRICT_UPDATE);
        $this->strictRemove = (bool) ($options & self::OPTION_STRICT_REMOVE);
    }

    public function addItem($item): void
    {
        $this->dataStorage->addItem($item);
    }

    public function containsItem($item): bool
    {
        return $this->dataStorage->containsItem($item);
    }

    public function getAllItems(int $offset = 0, ?int $limit = null): array
    {
        return $this->sliceItems($this->dataStorage->getAllItems(), $offset, $limit);
    }

    public function sliceItems(array $items, int $offset = 0, ?int $limit = null): array
    {
        return array_slice($items, $offset, $limit);
    }

    public function removeItem($item): void
    {
        if ($this->strictRemove && !$this->dataStorage->containsItem($item)) {
            throw ItemNotFoundException::createItemNotFoundException($item);
        }

        $this->dataStorage->removeItem($item);
    }

    public function setNamedItem(string $name, $item): void
    {
        $this->dataStorage->setNamedItem($name, $item);
    }

    public function namedItemExists(string $name): bool
    {
        return $this->dataStorage->namedItemExists($name);
    }

    public function getNamedItem(string $name)
    {
        if ($this->strictGet && !$this->dataStorage->namedItemExists($name)) {
            throw NamedItemNotFoundException::createNamedItemNotFoundException($name);
        }

        return $this->dataStorage->getNamedItem($name);
    }

    public function replaceNamedItem(string $name, $newItem): void
    {
        if ($this->strictUpdate && !$this->dataStorage->namedItemExists($name)) {
            throw NamedItemNotFoundException::createNamedItemNotFoundException($name);
        }

        $this->dataStorage->setNamedItem($name, $newItem);
    }

    public function removeNamedItem(string $name): void
    {
        if ($this->strictRemove && !$this->dataStorage->namedItemExists($name)) {
            throw NamedItemNotFoundException::createNamedItemNotFoundException($name);
        }

        $this->dataStorage->removeNamedItem($name);
    }

    public function getAllItemsByCriteria(array $criteria): array
    {
        return iterator_to_array($this->iterateMatchingItems($criteria), false);
    }

    public function getOneItemByCriteria(array $criteria)
    {
        foreach ($this->iterateMatchingItems($criteria) as $item) {
            return $item;
        }

        if ($this->strictGet) {
            throw new ItemNotFoundException('Cannot find item with matching criteria.');
        }

        return null;
    }

    public function updateAllItemsByCriteria(array $criteria, array $propertyUpdates): void
    {
        foreach ($this->iterateMatchingItems($criteria) as $matchedItem) {
            foreach ($propertyUpdates as $propertyName => $newValue) {
                $this->updatePropertyValue($matchedItem, $propertyName, $newValue);
            }
        }
    }

    public function updateOneByCriteria(array $criteria, array $propertyUpdates): void
    {
        foreach ($this->iterateMatchingItems($criteria) as $matchedItem) {
            foreach ($propertyUpdates as $propertyName => $newValue) {
                $this->updatePropertyValue($matchedItem, $propertyName, $newValue);
            }

            return;
        }

        if ($this->strictUpdate) {
            throw new ItemNotFoundException('Cannot find item with matching criteria.');
        }
    }

    private function updatePropertyValue($matchedItem, string $propertyName, $newValue): void
    {
        $modifiedItem = $this->propertyOperator->setPropertyValue($matchedItem, $propertyName, $newValue);

        // When the item identity changes after update (e.g. when it's array), replace the item in the data store
        if ($modifiedItem !== $matchedItem) {
            $this->dataStorage->replaceItem($matchedItem, $modifiedItem);
        }
    }

    public function removeAllItemsByCriteria(array $criteria): void
    {
        foreach ($this->iterateMatchingItems($criteria) as $matchedItem) {
            $this->dataStorage->removeItem($matchedItem);
        }
    }

    public function removeOneItemByCriteria(array $criteria): void
    {
        foreach ($this->iterateMatchingItems($criteria) as $matchedItem) {
            $this->dataStorage->removeItem($matchedItem);

            return;
        }

        if ($this->strictRemove) {
            throw new ItemNotFoundException('Cannot find item with matching criteria.');
        }
    }

    public function getAllItemsByPropertyValue(string $propertyName, $value): array
    {
        return $this->getAllItemsByCriteria([$propertyName => $value]);
    }

    public function getOneItemByPropertyValue(string $propertyName, $value)
    {
        return $this->getOneItemByCriteria([$propertyName => $value]);
    }

    public function updateAllItemsByPropertyValue(string $propertyName, $value, array $propertyUpdates): void
    {
        $this->updateAllItemsByCriteria([$propertyName => $value], $propertyUpdates);
    }

    public function updateOneByPropertyValue(string $propertyName, $value, array $propertyUpdates): void
    {
        $this->updateOneByCriteria([$propertyName => $value], $propertyUpdates);
    }

    public function removeAllItemsByPropertyValue(string $propertyName, $value): void
    {
        $this->removeAllItemsByCriteria([$propertyName => $value]);
    }

    public function removeOneItemByPropertyValue(string $propertyName, $value): void
    {
        $this->removeOneItemByCriteria([$propertyName => $value]);
    }

    public function sortItemsByPropertyValue(array $items, string $propertyName, int $order = self::SORT_ORDER_ASC): array
    {
        if (self::SORT_ORDER_DESC === $order) {
            usort($items, function ($a, $b) use ($propertyName) {
                return $this->propertyOperator->getPropertyValue($b, $propertyName) <=> $this->propertyOperator->getPropertyValue($a, $propertyName);
            });
        } else {
            usort($items, function ($a, $b) use ($propertyName) {
                return $this->propertyOperator->getPropertyValue($a, $propertyName) <=> $this->propertyOperator->getPropertyValue($b, $propertyName);
            });
        }

        return $items;
    }

    private function iterateMatchingItems(array $criteria): \Traversable
    {
        return $this->propertyOperator->getItemsWithMatchingCriteria($this->dataStorage->getAllItems(), $criteria);
    }
}
