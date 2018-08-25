<?php

namespace Scheb\InMemoryDataStorage\Repository;

use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;
use Scheb\InMemoryDataStorage\Exception\NamedItemNotFoundException;

class DataRepository
{
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
    private $strictDelete;

    public function __construct(DataStorageInterface $dataStorage, array $options = [])
    {
        $this->dataStorage = $dataStorage;
        $this->strictGet = $options['strictGet'] ?? true;
        $this->strictUpdate = $options['strictUpdate'] ?? true;
        $this->strictDelete = $options['strictDelete'] ?? true;
    }

    public function addItem($item): void
    {
        $this->dataStorage->addItem($item);
    }

    public function containsItem($item): bool
    {
        return $this->dataStorage->containsItem($item);
    }

    public function getAllItems()
    {
        return $this->dataStorage->getAllItems();
    }

    public function removeItem($item): void
    {
        if ($this->strictDelete && !$this->dataStorage->containsItem($item)) {
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
        if ($this->strictDelete && !$this->dataStorage->namedItemExists($name)) {
            throw NamedItemNotFoundException::createNamedItemNotFoundException($name);
        }

        $this->dataStorage->removeNamedItem($name);
    }
}
