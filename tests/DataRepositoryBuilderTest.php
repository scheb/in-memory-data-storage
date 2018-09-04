<?php

namespace Scheb\InMemoryDataStorage\Tests;

use Scheb\Comparator\Comparator;
use Scheb\Comparator\ValueComparisonStrategyInterface;
use Scheb\InMemoryDataStorage\DataRepositoryBuilder;
use Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage;
use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Matching\ValueMatcher;
use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperator;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperatorInterface;
use Scheb\InMemoryDataStorage\Test\TestCase;
use Scheb\PropertyAccess\PropertyAccess;
use Scheb\PropertyAccess\PropertyAccessInterface;
use Scheb\PropertyAccess\Strategy\ArrayAccessStrategy;
use Scheb\PropertyAccess\Strategy\ObjectGetterSetterAccessStrategy;
use Scheb\PropertyAccess\Strategy\ObjectPropertyAccessStrategy;
use Scheb\PropertyAccess\Strategy\PropertyAccessStrategyInterface;

class DataRepositoryBuilderTest extends TestCase
{
    /**
     * @var DataRepositoryBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new DataRepositoryBuilder();
    }

    /**
     * @test
     */
    public function build_defaults_defaultInstancesUsed(): void
    {
        $dataRepository = $this->builder->build();
        $this->assertAttributeEquals(false, 'strictGet', $dataRepository);
        $this->assertAttributeEquals(false, 'strictUpdate', $dataRepository);
        $this->assertAttributeEquals(false, 'strictRemove', $dataRepository);

        $this->assertAttributeInstanceOf(ArrayDataStorage::class, 'dataStorage', $dataRepository);
        $this->assertAttributeInstanceOf(PropertyOperator::class, 'propertyOperator', $dataRepository);
    }

    /**
     * @test
     */
    public function strictGet_enableOption_optionSetInDataRepository(): void
    {
        $dataRepository = $this->builder->strictGet()->build();
        $this->assertAttributeEquals(true, 'strictGet', $dataRepository);
    }

    /**
     * @test
     */
    public function strictUpdate_enableOption_optionSetInDataRepository(): void
    {
        $dataRepository = $this->builder->strictUpdate()->build();
        $this->assertAttributeEquals(true, 'strictUpdate', $dataRepository);
    }

    /**
     * @test
     */
    public function strictRemove_enableOption_optionSetInDataRepository(): void
    {
        $dataRepository = $this->builder->strictRemove()->build();
        $this->assertAttributeEquals(true, 'strictRemove', $dataRepository);
    }

    /**
     * @test
     */
    public function setDataStorage_useCustomDataStorage_usedInDataRepository(): void
    {
        $dataStorage = $this->createMock(DataStorageInterface::class);
        $dataRepository = $this->builder->setDataStorage($dataStorage)->build();
        $this->assertAttributeSame($dataStorage, 'dataStorage', $dataRepository);
    }

    /**
     * @test
     */
    public function setPropertyOperator_useCustomPropertyOperator_usedInDataRepository(): void
    {
        $propertyOperator = $this->createMock(PropertyOperatorInterface::class);
        $dataRepository = $this->builder->setPropertyOperator($propertyOperator)->build();
        $this->assertAttributeSame($propertyOperator, 'propertyOperator', $dataRepository);
    }

    /**
     * @test
     */
    public function setValueMatcherAndSetPropertyAccess_useBothCustomInstances_usedInDataRepository(): void
    {
        $valueMatcher = $this->createMock(ValueMatcherInterface::class);
        $propertyAccess = $this->createMock(PropertyAccessInterface::class);

        $dataRepository = $this->builder
            ->setValueMatcher($valueMatcher)
            ->setPropertyAccess($propertyAccess)
            ->build();

        $expectedPropertyOperator = new PropertyOperator($valueMatcher, $propertyAccess);
        $this->assertAttributeEquals($expectedPropertyOperator, 'propertyOperator', $dataRepository);
    }

    /**
     * @test
     */
    public function addComparisonStrategy_useCustomComparisionStrategy_usedInDataRepository(): void
    {
        $customStrategy = $this->createMock(ValueComparisonStrategyInterface::class);
        $propertyAccess = $this->createMock(PropertyAccessInterface::class);

        $dataRepository = $this->builder
            ->addComparisonStrategy($customStrategy)
            ->setPropertyAccess($propertyAccess) // Stub away complexity
            ->build();

        $expectedValueMatcher = new ValueMatcher(new Comparator(true, [$customStrategy]));
        $expectedPropertyOperator = new PropertyOperator(
            $expectedValueMatcher,
            $propertyAccess
        );
        $this->assertAttributeEquals($expectedPropertyOperator, 'propertyOperator', $dataRepository);
    }

    /**
     * @test
     */
    public function useStrictTypeComparison(): void
    {
        $propertyAccess = $this->createMock(PropertyAccessInterface::class);

        $dataRepository = $this->builder
            ->useStrictTypeComparison(false)
            ->setPropertyAccess($propertyAccess) // Stub away complexity
            ->build();

        $expectedValueMatcher = new ValueMatcher(new Comparator(false, []));
        $expectedPropertyOperator = new PropertyOperator(
            $expectedValueMatcher,
            $propertyAccess
        );
        $this->assertAttributeEquals($expectedPropertyOperator, 'propertyOperator', $dataRepository);
    }

    /**
     * @test
     */
    public function addPropertyAccessStrategy_appendCustomPropertyAccessStrategy_passedWithDefaultStrategies(): void
    {
        $valueMatcher = $this->createMock(ValueMatcherInterface::class);
        $customStrategy = $this->createMock(PropertyAccessStrategyInterface::class);

        $dataRepository = $this->builder
            ->addPropertyAccessStrategy($customStrategy)
            ->setValueMatcher($valueMatcher) // Stub away complexity
            ->build();

        $propertyAccessStrategies = [
            $customStrategy,
            new ArrayAccessStrategy(),
            new ObjectPropertyAccessStrategy(),
            new ObjectGetterSetterAccessStrategy(),
        ];

        $expectedPropertyOperator = new PropertyOperator($valueMatcher, new PropertyAccess($propertyAccessStrategies));
        $this->assertAttributeEquals($expectedPropertyOperator, 'propertyOperator', $dataRepository);
    }
}
