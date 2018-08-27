<?php

namespace Scheb\InMemoryDataStorage\Repository;

use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;
use Scheb\InMemoryDataStorage\Exception\NamedItemNotFoundException;

class DataRepository
{
    public const OPTION_STRICT_GET = 1;
    public const OPTION_STRICT_UPDATE = 2;
    public const OPTION_STRICT_REMOVE = 4;

    /**
     * @var DataStorageInterface
     */
    private $dataStorage;

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

    public function __construct(DataStorageInterface $dataStorage, int $options = 0)
    {
        $this->dataStorage = $dataStorage;
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

    public function getAllItems(): array
    {
        return $this->dataStorage->getAllItems();
    }

    public function removeItem($item): void
    {
        if ($this->strictRemove && !$this->dataStorage->containsItem($item)) {
            throw ItemNotFoundException::createItemNotFoundException($item);
        }

        $this->dataStorage->removeItem($item);
    }

    public function addNamedItem(string $name, $item): void
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
