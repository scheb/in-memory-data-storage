<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\InMemoryDataStorage\Exception\SetPropertyFailedException;
use Scheb\InMemoryDataStorage\Test\TestCase;

class PropertyAccessTest extends TestCase
{
    private const PROPERTY_NAME = 'property';

    /**
     * @var \stdClass
     */
    private $valueObject;

    /**
     * @var MockObject|PropertyAccessStrategyInterface
     */
    private $strategy1;

    /**
     * @var MockObject|PropertyAccessStrategyInterface
     */
    private $strategy2;

    /**
     * @var MockObject|PropertyAccessStrategyInterface
     */
    private $strategy3;

    /**
     * @var PropertyAccess
     */
    private $propertyAccess;

    const PROPERTY_VALUE = 'propertyValue';

    protected function setUp()
    {
        $this->valueObject = new \stdClass();
        $this->strategy1 = $this->createMock(PropertyAccessStrategyInterface::class);
        $this->strategy2 = $this->createMock(PropertyAccessStrategyInterface::class);
        $this->strategy3 = $this->createMock(PropertyAccessStrategyInterface::class);
        $this->propertyAccess = new PropertyAccess([$this->strategy1, $this->strategy2, $this->strategy3]);
    }

    private function assertStrategySupportsAndReturns(MockObject $strategy, bool $supports): void
    {
        $strategy
            ->expects($this->once())
            ->method('supports')
            ->with($this->valueObject)
            ->willReturn($supports);
    }

    private function assertStrategySupportsNotCalled(MockObject $strategy): void
    {
        $strategy
            ->expects($this->never())
            ->method('supports');
    }

    private function assertStrategyGetPropertyReturns(MockObject $strategy, $returnValue): void
    {
        $strategy
            ->expects($this->once())
            ->method('getPropertyValue')
            ->with($this->valueObject, self::PROPERTY_NAME)
            ->willReturn($returnValue);
    }

    private function assertStrategyGetPropertyNotCalled(MockObject $strategy): void
    {
        $strategy
            ->expects($this->never())
            ->method('getPropertyValue');
    }

    private function assertStrategySetPropertyReturns(MockObject $strategy, bool $returnValue): void
    {
        $strategy
            ->expects($this->once())
            ->method('setPropertyValue')
            ->with($this->valueObject, self::PROPERTY_NAME, self::PROPERTY_VALUE)
            ->willReturn($returnValue);
    }

    private function assertStrategySetPropertyNotCalled(MockObject $strategy): void
    {
        $strategy
            ->expects($this->never())
            ->method('setPropertyValue');
    }

    /**
     * @test
     */
    public function getPropertyValue_supportedStrategyReturnsNotNull_notCallOtherStrategies(): void
    {
        $this->assertStrategySupportsAndReturns($this->strategy1, false);
        $this->assertStrategyGetPropertyNotCalled($this->strategy1);

        $this->assertStrategySupportsAndReturns($this->strategy2, true);
        $this->assertStrategyGetPropertyReturns($this->strategy2, self::PROPERTY_VALUE);

        $this->assertStrategySupportsNotCalled($this->strategy3);
        $this->assertStrategyGetPropertyNotCalled($this->strategy3);

        $returnValue = $this->propertyAccess->getPropertyValue($this->valueObject, self::PROPERTY_NAME);
        $this->assertEquals(self::PROPERTY_VALUE, $returnValue);
    }

    /**
     * @test
     */
    public function getPropertyValue_allSupportedStrategiesReturnNull_callAllStrategiesReturnNull(): void
    {
        $this->assertStrategySupportsAndReturns($this->strategy1, false);
        $this->assertStrategyGetPropertyNotCalled($this->strategy1);

        $this->assertStrategySupportsAndReturns($this->strategy2, true);
        $this->assertStrategyGetPropertyReturns($this->strategy2, null);

        $this->assertStrategySupportsAndReturns($this->strategy3, true);
        $this->assertStrategyGetPropertyReturns($this->strategy3, null);

        $returnValue = $this->propertyAccess->getPropertyValue($this->valueObject, self::PROPERTY_NAME);
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function setPropertyValue_oneStrategyModifiesValue_notCallOtherStrategies(): void
    {
        $this->assertStrategySupportsAndReturns($this->strategy1, false);
        $this->assertStrategySetPropertyNotCalled($this->strategy1);

        $this->assertStrategySupportsAndReturns($this->strategy2, true);
        $this->assertStrategySetPropertyReturns($this->strategy2, true);

        $this->assertStrategySupportsNotCalled($this->strategy3);
        $this->assertStrategySetPropertyNotCalled($this->strategy3);

        $this->propertyAccess->setPropertyValue($this->valueObject, self::PROPERTY_NAME, self::PROPERTY_VALUE);
    }

    /**
     * @test
     */
    public function setPropertyValue_noStrategyWasSuccessful_throwSetPropertyFailedException(): void
    {
        $this->assertStrategySupportsAndReturns($this->strategy1, false);
        $this->assertStrategySetPropertyNotCalled($this->strategy1);

        $this->assertStrategySupportsAndReturns($this->strategy2, true);
        $this->assertStrategySetPropertyReturns($this->strategy2, false);

        $this->assertStrategySupportsAndReturns($this->strategy3, true);
        $this->assertStrategySetPropertyReturns($this->strategy3, false);

        $this->expectException(SetPropertyFailedException::class);
        $this->propertyAccess->setPropertyValue($this->valueObject, self::PROPERTY_NAME, self::PROPERTY_VALUE);
    }
}
