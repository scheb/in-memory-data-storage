<?php

namespace Scheb\InMemoryDataStorage;

use Scheb\Comparator\Comparator;
use Scheb\Comparator\ValueComparisonStrategyInterface;
use Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage;
use Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface;
use Scheb\InMemoryDataStorage\Matching\ValueMatcher;
use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperator;
use Scheb\InMemoryDataStorage\PropertyAccess\PropertyOperatorInterface;
use Scheb\PropertyAccess\PropertyAccess;
use Scheb\PropertyAccess\PropertyAccessInterface;
use Scheb\PropertyAccess\Strategy\PropertyAccessStrategyInterface;

class DataRepositoryBuilder
{
    /**
     * @var int
     */
    private $dataRepositoryOptions = 0;

    /**
     * @var DataStorageInterface
     */
    private $dataStorage;

    /**
     * @var PropertyOperatorInterface
     */
    private $propertyOperator;

    /**
     * @var ValueMatcherInterface
     */
    private $valueMatcher;

    /**
     * @var bool
     */
    private $useStrictTypeComparison = true;

    /**
     * @var ValueComparisonStrategyInterface[]
     */
    private $customComparisionStrategies = [];

    /**
     * @var PropertyAccessInterface
     */
    private $propertyAccess;

    /**
     * @var PropertyAccessStrategyInterface[]
     */
    private $customPropertyAccessStrategies = [];

    public function getDataRepository(): DataRepository
    {
        return new DataRepository(
            $this->createDataStorage(),
            $this->createPropertyOperator(),
            $this->dataRepositoryOptions
        );
    }

    public function setDataStorage(DataStorageInterface $dataStorage): self
    {
        $this->dataStorage = $dataStorage;

        return $this;
    }

    public function strictGet(): self
    {
        $this->dataRepositoryOptions |= DataRepository::OPTION_STRICT_GET;

        return $this;
    }

    public function strictUpdate(): self
    {
        $this->dataRepositoryOptions |= DataRepository::OPTION_STRICT_UPDATE;

        return $this;
    }

    public function strictRemove(): self
    {
        $this->dataRepositoryOptions |= DataRepository::OPTION_STRICT_REMOVE;

        return $this;
    }

    public function setPropertyOperator(PropertyOperatorInterface $propertyOperator): self
    {
        $this->propertyOperator = $propertyOperator;

        return $this;
    }

    public function setValueMatcher(ValueMatcherInterface $valueMatcher): self
    {
        $this->valueMatcher = $valueMatcher;

        return $this;
    }

    public function addComparisonStrategy(ValueComparisonStrategyInterface $comparisonStrategy): self
    {
        $this->customComparisionStrategies[] = $comparisonStrategy;

        return $this;
    }

    public function useStrictTypeComparison(bool $useStrictTypeComparison): self
    {
        $this->useStrictTypeComparison = $useStrictTypeComparison;

        return $this;
    }

    public function setPropertyAccess(PropertyAccessInterface $propertyAccess): self
    {
        $this->propertyAccess = $propertyAccess;

        return $this;
    }

    private function createDataStorage(): DataStorageInterface
    {
        if ($this->dataStorage) {
            return $this->dataStorage;
        }

        return new ArrayDataStorage();
    }

    private function createPropertyOperator(): PropertyOperatorInterface
    {
        if ($this->propertyOperator) {
            return $this->propertyOperator;
        }

        return new PropertyOperator($this->createValueMatcher(), $this->createPropertyAccess());
    }

    private function createValueMatcher(): ValueMatcherInterface
    {
        if ($this->valueMatcher) {
            return $this->valueMatcher;
        }

        $comparator = new Comparator($this->useStrictTypeComparison, $this->customComparisionStrategies);

        return new ValueMatcher($comparator);
    }

    private function createPropertyAccess(): PropertyAccessInterface
    {
        if ($this->propertyAccess) {
            return $this->propertyAccess;
        }

        return new PropertyAccess($this->customPropertyAccessStrategies);
    }
}
