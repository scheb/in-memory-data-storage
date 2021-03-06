<?php

namespace Scheb\InMemoryDataStorage\Test;

use Scheb\InMemoryDataStorage\DataRepository;
use Scheb\InMemoryDataStorage\DataRepositoryBuilder;

class DataRepositoryIntegrationTest extends TestCase
{
    private const ITEM_NAME = 'itemName';

    private $foo;
    private $bar;

    /**
     * @var DataRepository
     */
    private $dataRepository;

    protected function setUp()
    {
        $this->dataRepository = (new DataRepositoryBuilder())->build();
        $this->foo = new \stdClass();
        $this->bar = new \stdClass();
    }

    private function createPropertyObject(array $properties = []): \stdClass
    {
        $item = new \stdClass();
        foreach ($properties as $key => $value) {
            $item->{$key} = $value;
        }

        return $item;
    }

    /**
     * @test
     */
    public function addItem_singleItemAdded_dataStoreContainsThatItem(): void
    {
        $this->dataRepository->addItem($this->foo);

        $this->assertTrue($this->dataRepository->containsItem($this->foo));

        $allItems = $this->dataRepository->getAllItems();
        $this->assertCount(1, $allItems);
        $this->assertSame($this->foo, $allItems[0]);
    }

    /**
     * @test
     */
    public function removeItem_removePreviouslyAddedItem_noLongerContainedInDataStore()
    {
        $this->dataRepository->addItem($this->foo);
        $this->dataRepository->removeItem($this->foo);

        $this->assertFalse($this->dataRepository->containsItem($this->foo));
        $this->assertCount(0, $this->dataRepository->getAllItems());
    }

    /**
     * @test
     */
    public function addNamedItem_singleItemAdded_dataStoreContainsThatItem()
    {
        $this->dataRepository->setNamedItem(self::ITEM_NAME, $this->foo);

        $this->assertTrue($this->dataRepository->namedItemExists(self::ITEM_NAME));
        $this->assertTrue($this->dataRepository->containsItem($this->foo));

        $namedItem = $this->dataRepository->getNamedItem(self::ITEM_NAME);
        $this->assertSame($this->foo, $namedItem);

        $allItems = $this->dataRepository->getAllItems();
        $this->assertCount(1, $allItems);
        $this->assertSame($this->foo, $allItems[0]);
    }

    /**
     * @test
     */
    public function replaceNamedItem_replaceExistingItem_takesPlaceOfExistingItem()
    {
        $this->dataRepository->setNamedItem(self::ITEM_NAME, $this->foo);
        $this->dataRepository->replaceNamedItem(self::ITEM_NAME, $this->bar);

        $this->assertFalse($this->dataRepository->containsItem($this->foo));
        $this->assertTrue($this->dataRepository->containsItem($this->bar));

        $namedItem = $this->dataRepository->getNamedItem(self::ITEM_NAME);
        $this->assertSame($this->bar, $namedItem);

        $allItems = $this->dataRepository->getAllItems();
        $this->assertCount(1, $allItems);
        $this->assertContains($this->bar, $allItems);
    }

    /**
     * @test
     */
    public function removeNamedItem_removePreviouslyAddedItem_noLongerContainedInDataStore()
    {
        $this->dataRepository->setNamedItem(self::ITEM_NAME, $this->foo);
        $this->dataRepository->removeNamedItem(self::ITEM_NAME);

        $this->assertFalse($this->dataRepository->containsItem($this->bar));
        $this->assertFalse($this->dataRepository->namedItemExists(self::ITEM_NAME));
        $this->assertCount(0, $this->dataRepository->getAllItems());
    }

    /**
     * @test
     */
    public function updateOneByCriteria_modifyArrayItem_arrayItemWasReplacedInDataStore()
    {
        $this->dataRepository->addItem(['key' => 'value']);
        $this->dataRepository->updateOneByCriteria(['key' => 'value'], ['key' => 'newValue']);

        $allItems = $this->dataRepository->getAllItems();
        $this->assertEquals([['key' => 'newValue']], $allItems);
    }

    /**
     * @test
     */
    public function sortItemsByPropertyValue_sortAscending_returnCorrectOrder(): void
    {
        $item1 = $this->createPropertyObject(['property' => 1]);
        $item2 = $this->createPropertyObject(['property' => 2]);
        $item3 = $this->createPropertyObject(['property' => 3]);
        $itemNull = $this->createPropertyObject(['property' => null]);
        $itemEmpty = $this->createPropertyObject([]);

        $list = [$itemNull, $item2, $item1, $item3, $itemEmpty];
        $expectedList = [$itemNull, $itemEmpty, $item1, $item2, $item3];

        $returnedList = $this->dataRepository->sortItemsByPropertyValue($list, 'property', DataRepository::SORT_ORDER_ASC);
        $this->assertEquals($expectedList, $returnedList);
    }

    /**
     * @test
     */
    public function sortItemsByPropertyValue_sortDescending_returnCorrectOrder(): void
    {
        $item1 = $this->createPropertyObject(['property' => 1]);
        $item2 = $this->createPropertyObject(['property' => 2]);
        $item3 = $this->createPropertyObject(['property' => 3]);
        $itemNull = $this->createPropertyObject(['property' => null]);
        $itemEmpty = $this->createPropertyObject([]);

        $list = [$itemNull, $item2, $item1, $item3, $itemEmpty];
        $expectedList = [$item3, $item2, $item1, $itemNull, $itemEmpty];

        $returnedList = $this->dataRepository->sortItemsByPropertyValue($list, 'property', DataRepository::SORT_ORDER_DESC);
        $this->assertEquals($expectedList, $returnedList);
    }
}
