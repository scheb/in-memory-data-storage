<?php

declare(strict_types=1);

namespace Scheb\InMemoryDataStorage\Query;

use Scheb\InMemoryDataStorage\DataRepository;

class Query
{
    public const SORT_ORDER_ASC = DataRepository::SORT_ORDER_ASC;
    public const SORT_ORDER_DESC = DataRepository::SORT_ORDER_DESC;

    /**
     * @var array
     */
    private $fieldCriteria = [];

    /**
     * @var callable
     */
    private $criteriaCallback;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $fieldSort = [];

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    public function getFieldCriteria(): array
    {
        return $this->fieldCriteria;
    }

    public function setFieldCriteria(string $fieldName, callable $callback): void
    {
        $this->fieldCriteria[$fieldName][] = $callback;
    }

    public function getFieldSort(): array
    {
        return $this->fieldSort;
    }

    public function setFieldSort(string $fieldName, int $order): void
    {
        $this->fieldSort[$fieldName] = $order;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getCriteriaCallback(): ?callable
    {
        return $this->criteriaCallback;
    }

    public function setCriteriaCallback(callable $criteriaCallback): void
    {
        $this->criteriaCallback = $criteriaCallback;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
