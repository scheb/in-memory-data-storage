<?php

namespace Scheb\InMemoryDataStorage\DataStorage;

class ArrayDataStorage implements DataStorageInterface
{
    private $items = [];

    public function addItem($item): void
    {
        $this->items[] = $item;
    }

    public function containsItem($item): bool
    {
        return in_array($item, $this->items, true);
    }

    public function removeItem($item): bool
    {
        $key = array_search($item, $this->items, true);
        if (false === $key) {
            return false;
        }

        unset($this->items[$key]);

        return true;
    }

    public function getAllItems(): array
    {
        return array_values($this->items);
    }

    public function setNamedItem(string $name, $item): void
    {
        $this->items[$name] = $item;
    }

    public function getNamedItem(string $name)
    {
        if (!$this->namedItemExists($name)) {
            return null;
        }

        return $this->items[$name];
    }

    public function namedItemExists(string $name): bool
    {
        return isset($this->items[$name]);
    }

    public function removeNamedItem(string $name): bool
    {
        if (!$this->namedItemExists($name)) {
            return false;
        }

        unset($this->items[$name]);

        return true;
    }
}
