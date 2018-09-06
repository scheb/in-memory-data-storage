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

    public function replaceItem($item, $newItem): void
    {
        $key = array_search($item, $this->items, true);
        if (false !== $key) {
            $this->items[$key] = $newItem;
        }
    }

    public function removeItem($item): void
    {
        $key = array_search($item, $this->items, true);
        if (false !== $key) {
            unset($this->items[$key]);
        }
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

    public function removeNamedItem(string $name): void
    {
        unset($this->items[$name]);
    }
}
