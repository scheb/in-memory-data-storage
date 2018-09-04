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
        $this->strictGet = $options & self::OPTION_STRICT_GET;
        $this->strictUpdate = $options & self::OPTION_STRICT_UPDATE;
        $this->strictRemove = $options & self::OPTION_STRICT_REMOVE;
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
}
