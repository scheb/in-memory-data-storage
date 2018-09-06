<?php

namespace Scheb\InMemoryDataStorage\DataStorage;

interface DataStorageInterface
{
    /**
     * Add a single item to the data storage.
     *
     * @param mixed $item
     */
    public function addItem($item): void;

    /**
     * Check if a specific item exists in the data storage.
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function containsItem($item): bool;

    /**
     * Replace an item with a different item.
     *
     * @param mixed $item
     * @param mixed $newItem
     */
    public function replaceItem($item, $newItem): void;

    /**
     * Remove a specific item from the data storage.
     *
     * @param mixed $item
     */
    public function removeItem($item): void;

    /**
     * Return all items form the data storage.
     *
     * @return array
     */
    public function getAllItems(): array;

    /**
     * Add a named item to the data storage.
     *
     * @param string $name
     * @param mixed  $item
     */
    public function setNamedItem(string $name, $item): void;

    /**
     * Get a named item from the data storage.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getNamedItem(string $name);

    /**
     * Check if an item with that name exists in the data storage.
     *
     * @param string $name
     *
     * @return bool
     */
    public function namedItemExists(string $name): bool;

    /**
     * Remove a named item from the data storage.
     *
     * @param string $name
     */
    public function removeNamedItem(string $name): void;
}
