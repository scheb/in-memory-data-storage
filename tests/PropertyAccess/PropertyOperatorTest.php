<?php

namespace Scheb\InMemoryDataStorage\Test\PropertyAccess;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperator;
use Scheb\InMemoryDataStorage\Test\TestCase;
use Scheb\PropertyAccess\PropertyAccessInterface;

class PropertyOperatorTest extends TestCase
{
    /**
     * @var MockObject|ValueMatcherInterface
     */
    private $valueMatcher;

    /**
     * @var MockObject|PropertyAccessInterface
     */
    private $propertyAccess;

    /**
     * @var PropertyOperator
     */
    private $propertyOperator;

    /**
     * @var \stdClass
     */
    private $valueObject;

    protected function setUp()
    {
        $this->valueMatcher = $this->createMock(ValueMatcherInterface::class);
        $this->propertyAccess = $this->createMock(PropertyAccessInterface::class);
        $this->propertyOperator = new PropertyOperator($this->valueMatcher, $this->propertyAccess);
        $this->valueObject = new \stdClass();
    }

    private function assertGetPropertyValue($valueObject, string $propertyName, $propertyValue = null): void
    {
        $this->propertyAccess
            ->expects($this->once())
            ->method('getPropertyValue')
            ->with($this->identicalTo($valueObject), $propertyName)
            ->willReturn($propertyValue);
    }

    private function assertSetPropertyValue($valueObject, string $propertyName, $newPropertyValue): void
    {
        $this->propertyAccess
            ->expects($this->once())
            ->method('setPropertyValue')
            ->with($this->identicalTo($valueObject), $propertyName, $newPropertyValue)
            ->willReturn($valueObject);
    }

    /**
     * @test
     */
    public function getPropertyValue_retrievePropertyFromValueObject_returnResultFromPropertyAccess(): void
    {
        $this->assertGetPropertyValue($this->valueObject, 'propertyName', 'value');
        $returnValue = $this->propertyOperator->getPropertyValue($this->valueObject, 'propertyName');
        $this->assertEquals('value', $returnValue);
    }

    /**
     * @test
     */
    public function setPropertyValue_modifyPropertyOnValueObject_returnModifedValueObjectFromPropertyAccess(): void
    {
        $this->assertSetPropertyValue($this->valueObject, 'propertyName', 'newValue');
        $returnValue = $this->propertyOperator->setPropertyValue($this->valueObject, 'propertyName', 'newValue');
        $this->assertSame($this->valueObject, $returnValue);
    }

    /**
     * @test
     */
    public function getItemsWithMatchingCriteria_itemPropertyNoMatch_returnEmptyIterator(): void
    {
        $this->propertyAccess
            ->expects($this->any())
            ->method('getPropertyValue')
            ->willReturn('value');

        $this->valueMatcher
            ->expects($this->any())
            ->method('match')
            ->willReturn(false);

        $iterator = $this->propertyOperator->getItemsWithMatchingCriteria([$this->valueObject], ['propertyName' => 'value']);
        $resultItems = iterator_to_array($iterator); // Trigger the generator

        $this->assertCount(0, $resultItems);
    }

    /**
     * @test
     */
    public function getItemsWithMatchingCriteria_findAllMatchingItems_returnMatchingItems(): void
    {
        $item1 = 'item1';
        $item2 = 'item2';
        $item3 = 'item3';
        $items = [$item1, $item2, $item3];

        $this->propertyAccess
            ->expects($this->exactly(3))
            ->method('getPropertyValue')
            ->withConsecutive(
                ['item1', 'propertyName'],
                ['item2', 'propertyName'],
                ['item3', 'propertyName']
            )
            ->willReturnMap([
                ['item1', 'propertyName', 'item1Value'],
                ['item2', 'propertyName', 'item2Value'],
                ['item3', 'propertyName', 'item3Value'],
            ]);

        $this->valueMatcher
            ->expects($this->exactly(3))
            ->method('match')
            ->withConsecutive(
                ['item1Value', 'value'],
                ['item2Value', 'value'],
                ['item3Value', 'value']
            )
            ->willReturnMap([
                ['item1Value', 'value', false],
                ['item2Value', 'value', true],
                ['item3Value', 'value', true],
            ]);

        $iterator = $this->propertyOperator->getItemsWithMatchingCriteria($items, ['propertyName' => 'value']);
        $resultItems = iterator_to_array($iterator); // Trigger the generator

        $this->assertCount(2, $resultItems);
        $this->assertContains('item2', $resultItems);
        $this->assertContains('item3', $resultItems);
    }

    /**
     * @test
     */
    public function getItemsWithMatchingCriteria_criteriaCallbackFunction_callFunction(): void
    {
        $this->propertyAccess
            ->expects($this->any())
            ->method('getPropertyValue')
            ->willReturn('value');

        $callbackArguments = null;
        $callbackFunction = function () use (&$callbackArguments) {
            $callbackArguments = func_get_args();

            return false;
        };

        $iterator = $this->propertyOperator->getItemsWithMatchingCriteria([$this->valueObject], ['propertyName' => $callbackFunction]);
        iterator_to_array($iterator); // Trigger the generator

        $this->assertNotNull($callbackArguments);
        $this->assertCount(2, $callbackArguments);
        $this->assertEquals('value', $callbackArguments[0]);
        $this->assertSame($this->valueMatcher, $callbackArguments[1]);
    }

    /**
     * @test
     */
    public function getItemsWithMatchingCriteria_criteriaCallbackFunction_useResultOfCallback(): void
    {
        $item1 = 'item1';
        $item2 = 'item2';
        $item3 = 'item3';
        $items = [$item1, $item2, $item3];

        $this->propertyAccess
            ->expects($this->any())
            ->method('getPropertyValue')
            ->willReturnMap([
                ['item1', 'propertyName', 'item1Value'],
                ['item2', 'propertyName', 'item2Value'],
                ['item3', 'propertyName', 'item3Value'],
            ]);

        $callbackFunction = function ($value) {
            $valueResultMap = [
                'item1Value' => false,
                'item2Value' => true,
                'item3Value' => true,
            ];

            return $valueResultMap[$value] ?? false;
        };

        $iterator = $this->propertyOperator->getItemsWithMatchingCriteria($items, ['propertyName' => $callbackFunction]);
        $resultItems = iterator_to_array($iterator); // Trigger the generator

        $this->assertCount(2, $resultItems);
        $this->assertContains('item2', $resultItems);
        $this->assertContains('item3', $resultItems);
    }
}
