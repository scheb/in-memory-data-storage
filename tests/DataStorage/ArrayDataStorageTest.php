<?php

namespace Scheb\InMemoryDataStorage\Test\DataStorage;

use Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage;
use Scheb\InMemoryDataStorage\Test\TestCase;

class ArrayDataStorageTest extends TestCase
{
    /**
     * @var ArrayDataStorage
     */
    private $dataStorage;

    public function setUp()
    {
        $this->dataStorage = new ArrayDataStorage();
    }

    /**
     * @test
     */
    public function addItem_addMultipleItem_getAllItemsReturnsItems(): void
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $this->dataStorage->addItem($item1);
        $this->dataStorage->addItem($item2);
        $allItems = $this->dataStorage->getAllItems();
        $this->assertCount(2, $allItems);
        $this->assertContains($item1, $allItems);
        $this->assertContains($item2, $allItems);
    }

    /**
     * @test
     */
    public function containsItem_itemInDataStorage_returnTrue(): void
    {
        $item = new \stdClass();
        $this->dataStorage->addItem($item);
        $this->assertTrue($this->dataStorage->containsItem($item));
    }

    /**
     * @test
     */
    public function containsItem_notInDataStorage_returnFalse(): void
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $this->dataStorage->addItem($item1);
        $this->assertFalse($this->dataStorage->containsItem($item2));
    }

    /**
     * @test
     */
    public function containsItem_namedItemInDataStorage_returnTrue(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->assertTrue($this->dataStorage->containsItem($item));
    }

    /**
     * @test
     */
    public function containsItem_namedItemNotInDataStorage_returnFalse(): void
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item1);
        $this->assertFalse($this->dataStorage->containsItem($item2));
    }

    /**
     * @test
     */
    public function removeItem_removeExistingItem_itemRemovedFromDataStorage(): void
    {
        $item = new \stdClass();
        $this->dataStorage->addItem($item);
        $this->dataStorage->removeItem($item);
        $this->assertCount(0, $this->dataStorage->getAllItems(), 'Data storage must be empty.');
    }

    /**
     * @test
     */
    public function removeItem_removeNamedItem_itemRemovedFromDataStorage(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->dataStorage->removeItem($item);
        $this->assertCount(0, $this->dataStorage->getAllItems(), 'Data storage must be empty.');
    }

    /**
     * @test
     */
    public function setNamedItem_addOneNamedItem_getAllItemsReturnsOneItem(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $allItems = $this->dataStorage->getAllItems();
        $this->assertCount(1, $allItems, 'Data storage must contain a single item.');
        $this->assertContains($item, $allItems);
    }

    /**
     * @test
     */
    public function setNamedItem_containsItem_returnsTrue(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->assertTrue($this->dataStorage->containsItem($item));
    }

    /**
     * @test
     */
    public function setNamedItem_setDifferentItem_namedItemIsReplaced(): void
    {
        $item1 = new \stdClass();
        $item2 = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item1);
        $this->dataStorage->setNamedItem('test', $item2);
        $returnValue = $this->dataStorage->getNamedItem('test');
        $this->assertSame($item2, $returnValue);
    }

    /**
     * @test
     */
    public function getNamedItem_namedItemExists_returnItem(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $returnValue = $this->dataStorage->getNamedItem('test');
        $this->assertSame($item, $returnValue);
    }

    /**
     * @test
     */
    public function getNamedItem_namedItemNotExists_returnNull(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $returnValue = $this->dataStorage->getNamedItem('otherName');
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function namedItemExists_namedItemInDataStorage_returnTrue(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->assertTrue($this->dataStorage->namedItemExists('test'));
    }

    /**
     * @test
     */
    public function namedItemExists_namedItemNotInDataStorage_returnFalse(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->assertFalse($this->dataStorage->namedItemExists('otherName'));
    }

    /**
     * @test
     */
    public function removeNamedItem_namedItemExists_removedFromDataStorage(): void
    {
        $item = new \stdClass();
        $this->dataStorage->setNamedItem('test', $item);
        $this->dataStorage->removeNamedItem('test');
        $this->assertCount(0, $this->dataStorage->getAllItems(), 'Data storage must be empty.');
    }
}
