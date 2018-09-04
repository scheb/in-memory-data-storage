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
    private $testItem;

    protected function setUp()
    {
        $this->dataStorage = $this->createMock(DataStorageInterface::class);
        $this->propertyOperator = $this->createMock(PropertyOperatorInterface::class);
        $this->dataRepository = new \Scheb\InMemoryDataStorage\DataRepository($this->dataStorage, $this->propertyOperator);
        $this->testItem = new \stdClass();
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
        $this->dataRepository->setOptions(\Scheb\InMemoryDataStorage\DataRepository::OPTION_STRICT_REMOVE);
    }

    private function stubGetAllItemsReturns(array $returnValue): void
    {
        $this->dataStorage
            ->expects($this->any())
            ->method('getAllItems')
            ->willReturn($returnValue);
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

    /**
     * @test
     */
    public function addItem_addOneItem_addItToDataStorage(): void
    {
        $this->dataStorage
            ->expects($this->once())
            ->method('addItem')
            ->with($this->identicalTo($this->testItem));

        $this->dataRepository->addItem($this->testItem);
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
            ->with($this->identicalTo($this->testItem))
            ->willReturn($dataStorageReturnValue);

        $returnValue = $this->dataRepository->containsItem($this->testItem);
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
        $allItems = [$this->testItem];
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
            ->with($this->identicalTo($this->testItem));

        $this->dataRepository->removeItem($this->testItem);
    }

    /**
     * @test
     */
    public function removeItem_strictRemoveOptionRemovalFails_throwItemNotFoundException(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubContainsItemReturns(false);

        $this->expectException(ItemNotFoundException::class);
        $this->dataRepository->removeItem($this->testItem);
    }

    /**
     * @test
     */
    public function removeItem_strictRemoveOptionRemovalSuccessful_noException(): void
    {
        $this->configureRepositoryStrictRemove();
        $this->stubContainsItemReturns(true);

        $this->dataRepository->removeItem($this->testItem);
        $this->expectNotToPerformAssertions();
    }

    /**
     * @test
     */
    public function setNamedItem_addOneNamedItem_setNamedItem(): void
    {
        $this->expectSetNamedItem('name', $this->testItem);
        $this->dataRepository->setNamedItem('name', $this->testItem);
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
            ->willReturn($this->testItem);

        $returnValue = $this->dataRepository->getNamedItem('name');
        $this->assertSame($this->testItem, $returnValue);
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
        $this->expectSetNamedItem('name', $this->testItem);
        $this->dataRepository->replaceNamedItem('name', $this->testItem);
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

        $this->expectSetNamedItem('name', $this->testItem);
        $this->dataRepository->replaceNamedItem('name', $this->testItem);
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
        $this->dataRepository->replaceNamedItem('name', $this->testItem);
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
}
