<?php

namespace Scheb\InMemoryDataStorage\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\InMemoryDataStorage\DataRepository;
use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;
use Scheb\InMemoryDataStorage\Exception\NamedItemNotFoundException;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperatorInterface;

class DataRepositoryTest extends TestCase
{
    /**
     * @var MockObject|DataStorageInterface
     */
    private $dataStorage;

    /**
     * @var MockObject|PropertyOperatorInterface
     */
    private $propertyOperator;

    /**
     * @var DataRepository
     */
    private $dataRepository;

    /**
     * @var \stdClass
     */
    private $item1;

    /**
     * @var \stdClass
     */
    private $item2;

    /**
     * @var \stdClass
     */
    private $item3;

    /**
     * @var array
     */
    private $allItems;

    protected function setUp()
    {
        $this->dataStorage = $this->createMock(DataStorageInterface::class);
        $this->propertyOperator = $this->createMock(PropertyOperatorInterface::class);
        $this->dataRepository = new \Scheb\InMemoryDataStorage\DataRepository($this->dataStorage, $this->propertyOperator);
        $this->item1 = new \stdClass();
        $this->item2 = new \stdClass();
        $this->item3 = new \stdClass();
        $this->allItems = [$this->item1, $this->item2, $this->item3];
    }

    private function configureRepositoryStrictGet(): void
    {
        $this->dataRepository->setOptions(DataRepository::OPTION_STRICT_GET);
    }

    private function configureRepositoryStrictUpdate(): void
    {
        $this->dataRepository->setOptions(DataRepository::OPTION_STRICT_UPDATE);
    }

    private function configureRepositoryStrictRemove(): void
    {
        $this->dataRepository->setOptions(DataRepository::OPTION_STRICT_REMOVE);
    }

    private function stubGetAllItemsReturns(array $returnValue): void
    {
        $this->dataStorage
            ->expects($this->any())
            ->method('getAllItems')
            ->willReturn($returnValue);
    }

    private function stubGetAllItemsReturnsItems123(): void
    {
        $this->stubGetAllItemsReturns($this->allItems);
    }

    private function stubContainsItemReturns(bool $returnValue): void
    {
        $this->dataStorage
            ->expects($this->any())
            ->method('containsItem')
            ->willReturn($returnValue);
    }

    private function stubGetNamedItemReturns($returnValue): void
    {
        $this->dataStorage
            ->expects($this->any())
            ->method('getNamedItem')
            ->willReturn($returnValue);
    }

    private function stubItemPropertyValues(array $valueMap): void
    {
        $this->propertyOperator
            ->expects($this->any())
            ->method('getPropertyValue')
            ->willReturnMap($valueMap);
    }

    private function expectSetNamedItem(string $name, $item): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('setNamedItem')
            ->with($name, $this->identicalTo($item));
    }

    private function expectRemoveNamedItem(string $name): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('removeNamedItem')
            ->with($name);
    }

    private function expectIterateMatchingItemsReturns(array $criteria, array $result): void
    {
        $this->propertyOperator
            ->expects($this->once())
            ->method('getItemsWithMatchingCriteria')
            ->with($this->allItems, $criteria)
            ->willReturn(new \ArrayIterator($result));
    }

    /**
     * @test
     */
    public function addItem_addOneItem_addItToDataStorage(): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('addItem')
            ->with($this->identicalTo($this->item1));

        $this->dataRepository->addItem($this->item1);
    }

    /**
     * @test
     * @dataProvider provideContainsItemReturnValues
     */
    public function containsItem_differentReturnValues_returnResultFromDataStorage(bool $dataStorageReturnValue): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('containsItem')
            ->with($this->identicalTo($this->item1))
            ->willReturn($dataStorageReturnValue);

        $returnValue = $this->dataRepository->containsItem($this->item1);
        $this->assertEquals($dataStorageReturnValue, $returnValue);
    }

    public function provideContainsItemReturnValues(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test
     */
    public function getAllItems_dataStorageHoldsItems_returnItemsFromDataStorage(): void
    {
        $allItems = [$this->item1];
        $this->stubGetAllItemsReturns($allItems);

        $returnValue = $this->dataRepository->getAllItems();
        $this->assertEquals($allItems, $returnValue);
    }

    /**
     * @test
     */
    public function removeItem_removeSpecificItem_removeFromDataStorage(): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('removeItem')
            ->with($this->identicalTo($this->item1));

        $this->dataRepository->removeItem($this->item1);
    }

    /**
     * @test
     */
    public function removeItem_strictRemoveOptionRemovalFails_throwItemNotFoundException(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubContainsItemReturns(false);

        $this->expectException(ItemNotFoundException::class);
        $this->dataRepository->removeItem($this->item1);
    }

    /**
     * @test
     */
    public function removeItem_strictRemoveOptionRemovalSuccessful_noException(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubContainsItemReturns(true);

        $this->dataRepository->removeItem($this->item1);
        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function setNamedItem_addOneNamedItem_setNamedItem(): void
    {
        $this->expectSetNamedItem('name', $this->item1);
        $this->dataRepository->setNamedItem('name', $this->item1);
    }

    /**
     * @test
     * @dataProvider provideNamedItemExistsReturnValues
     */
    public function namedItemExists_differentReturnValues_returnResultFromDataStorage(bool $dataStorageReturnValue): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('namedItemExists')
            ->with('name')
            ->willReturn($dataStorageReturnValue);

        $returnValue = $this->dataRepository->namedItemExists('name');
        $this->assertEquals($dataStorageReturnValue, $returnValue);
    }

    public function provideNamedItemExistsReturnValues(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test
     */
    public function getNamedItem_itemExists_returnThatItem(): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('getNamedItem')
            ->with('name')
            ->willReturn($this->item1);

        $returnValue = $this->dataRepository->getNamedItem('name');
        $this->assertSame($this->item1, $returnValue);
    }

    /**
     * @test
     */
    public function getNamedItem_itemNotExists_returnNull(): void
    {
        $this->stubGetNamedItemReturns(null);

        $returnValue = $this->dataRepository->getNamedItem('name');
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function getNamedItem_strictGetOptionItemNotExists_throwNamedItemNotFoundException(): void
    {
        $this->configureRepositoryStrictGet();
        $this->stubGetNamedItemReturns(null);

        $this->expectException(NamedItemNotFoundException::class);
        $this->dataRepository->getNamedItem('name');
    }

    /**
     * @test
     */
    public function replaceNamedItem_scriptUpdateOptionDisabled_setNamedItemAnyways(): void
    {
        $this->expectSetNamedItem('name', $this->item1);
        $this->dataRepository->replaceNamedItem('name', $this->item1);
    }

    /**
     * @test
     */
    public function replaceNamedItem_strictUpdateOptionNameExists_replaceItem(): void
    {
        $this->configureRepositoryStrictUpdate();

        $this->dataStorage
            ->expects($this->once())
            ->method('namedItemExists')
            ->with('name')
            ->willReturn(true);

        $this->expectSetNamedItem('name', $this->item1);
        $this->dataRepository->replaceNamedItem('name', $this->item1);
    }

    /**
     * @test
     */
    public function replaceNamedItem_strictUpdateOptionNameNotExists_throwNamedItemNotFoundException(): void
    {
        $this->configureRepositoryStrictUpdate();

        $this->dataStorage
            ->expects($this->once())
            ->method('namedItemExists')
            ->with('name')
            ->willReturn(false);

        $this->dataStorage
            ->expects($this->never())
            ->method('setNamedItem');

        $this->expectException(NamedItemNotFoundException::class);
        $this->dataRepository->replaceNamedItem('name', $this->item1);
    }

    /**
     * @test
     */
    public function removeNamedItem_scriptUpdateOptionDisabled_removeNamedItemAnyways(): void
    {
        $this->expectRemoveNamedItem('name');
        $this->dataRepository->removeNamedItem('name');
    }

    /**
     * @test
     */
    public function removeNamedItem_strictRemoveOptionNameExists_removeNamedItem(): void
    {
        $this->configureRepositoryStrictRemove();

        $this->dataStorage
            ->expects($this->once())
            ->method('namedItemExists')
            ->with('name')
            ->willReturn(true);

        $this->expectRemoveNamedItem('name');
        $this->dataRepository->removeNamedItem('name');
    }

    /**
     * @test
     */
    public function removeNamedItem_strictRemoveOptionNameNotExists_throwNamedItemNotFoundException(): void
    {
        $this->configureRepositoryStrictRemove();

        $this->dataStorage
            ->expects($this->once())
            ->method('namedItemExists')
            ->with('name')
            ->willReturn(false);

        $this->dataStorage
            ->expects($this->never())
            ->method('removeNamedItem');

        $this->expectException(NamedItemNotFoundException::class);
        $this->dataRepository->removeNamedItem('name');
    }

    /**
     * @test
     */
    public function getAllItemsByCriteria_multipleMatchingItems_returnAllItems(): void
    {
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $returnValue = $this->dataRepository->getAllItemsByCriteria(['property' => 'value']);
        $this->assertEquals([$this->item1, $this->item2], $returnValue);
    }

    /**
     * @test
     */
    public function getOneItemByCriteria_multipleMatchingItems_returnFirstItem(): void
    {
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $returnValue = $this->dataRepository->getOneItemByCriteria(['property' => 'value']);
        $this->assertSame($this->item1, $returnValue);
    }

    /**
     * @test
     */
    public function getOneItemByCriteria_noMatchingItem_returnNull(): void
    {
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], []);

        $returnValue = $this->dataRepository->getOneItemByCriteria(['property' => 'value']);
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function getOneItemByCriteria_strictGetOptionNoMatchingItem_throwItemNotFoundException(): void
    {
        $this->configureRepositoryStrictGet();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], []);

        $this->expectException(ItemNotFoundException::class);
        $this->dataRepository->getOneItemByCriteria(['property' => 'value']);
    }

    /**
     * @test
     */
    public function updateAllItemsByCriteria_multipleMatchingItems_setPropertyValues(): void
    {
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $this->propertyOperator
            ->expects($this->exactly(4))
            ->method('setPropertyValue')
            ->withConsecutive(
                [$this->item1, 'change1', 'value1'],
                [$this->item1, 'change2', 'value2'],
                [$this->item2, 'change1', 'value1'],
                [$this->item2, 'change2', 'value2']
            )
            ->willReturnArgument(0);

        $this->dataRepository->updateAllItemsByCriteria(['property' => 'value'], ['change1' => 'value1', 'change2' => 'value2']);
    }

    /**
     * @test
     */
    public function updateOneByCriteria_multipleMatches_updateFirstItem(): void
    {
        $this->configureRepositoryStrictUpdate();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $this->propertyOperator
            ->expects($this->exactly(2))
            ->method('setPropertyValue')
            ->withConsecutive(
                [$this->item1, 'change1', 'value1'],
                [$this->item1, 'change2', 'value2']
            )
            ->willReturnArgument(0);

        $this->dataRepository->updateOneByCriteria(['property' => 'value'], ['change1' => 'value1', 'change2' => 'value2']);
    }

    /**
     * @test
     */
    public function updateOneByCriteria_strictUpdateOptionNoMatchingItem_throwItemNotFoundException(): void
    {
        $this->configureRepositoryStrictUpdate();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], []);

        $this->expectException(ItemNotFoundException::class);
        $this->dataRepository->updateOneByCriteria(['property' => 'value'], ['change1' => 'value1', 'change2' => 'value2']);
    }

    /**
     * @test
     */
    public function removeAllItemsByCriteria_multipleMatches_removeAllItems(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $this->dataStorage
            ->expects($this->exactly(2))
            ->method('removeItem')
            ->withConsecutive(
                $this->item1,
                $this->item2
            );

        $this->dataRepository->removeAllItemsByCriteria(['property' => 'value']);
    }

    /**
     * @test
     */
    public function removeOneItemByCriteria_multipleMatches_removeFirstItem(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], [$this->item1, $this->item2]);

        $this->dataStorage
            ->expects($this->once())
            ->method('removeItem')
            ->with($this->item1);

        $this->dataRepository->removeOneItemByCriteria(['property' => 'value']);
    }

    /**
     * @test
     */
    public function removeOneItemByCriteria_strictRemoveOptionNoMatchingItem_throwItemNotFoundException(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubGetAllItemsReturnsItems123();
        $this->expectIterateMatchingItemsReturns(['property' => 'value'], []);

        $this->expectException(ItemNotFoundException::class);
        $this->dataRepository->removeOneItemByCriteria(['property' => 'value']);
    }

    /**
     * @test
     */
    public function sortItemsByPropertyValue_sortAscending_returnCorrectOrder(): void
    {
        $this->stubItemPropertyValues([
            [$this->item1, 'property', 1],
            [$this->item2, 'property', 2],
            [$this->item3, 'property', 3],
        ]);

        $list = [$this->item2, $this->item1, $this->item3];
        $expectedList = [$this->item1, $this->item2, $this->item3];

        $returnedList = $this->dataRepository->sortItemsByPropertyValue($list, 'property', DataRepository::SORT_ORDER_ASC);
        $this->assertEquals($expectedList, $returnedList);
    }

    /**
     * @test
     */
    public function sortItemsByPropertyValue_sortDescending_returnCorrectOrder(): void
    {
        $this->stubItemPropertyValues([
            [$this->item1, 'property', 1],
            [$this->item2, 'property', 2],
            [$this->item3, 'property', 3],
        ]);

        $list = [$this->item2, $this->item1, $this->item3];
        $expectedList = [$this->item3, $this->item2, $this->item1];

        $returnedList = $this->dataRepository->sortItemsByPropertyValue($list, 'property', DataRepository::SORT_ORDER_DESC);
        $this->assertEquals($expectedList, $returnedList);
    }
}
