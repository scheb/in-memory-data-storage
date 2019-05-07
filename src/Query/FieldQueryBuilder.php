<?php

declare(strict_types=1);

namespace Scheb\InMemoryDataStorage\Query;

use Scheb\InMemoryDataStorage\Comparison;

class FieldQueryBuilder
{
    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * The current field we are operating on
     *
     * @var string
     */
    private $currentField;

    public function __construct(QueryBuilder $builder, string $currentField)
    {
        $this->builder = $builder;
        $this->currentField = $currentField;
    }

    //Null
    public function isNull(): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::isNull());
    }

    public function notNull(): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::notNull());
    }

    // Equals
    public function equalTo($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::equals($value));
    }

    public function notEqualTo($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::not(Comparison::equals($value)));
    }

    // Numbers
    public function lessThan($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::lessThan($value));
    }

    public function lessThanOrEqualTo($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::lessThanOrEqual($value));
    }

    public function greaterThan($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::greaterThan($value));
    }

    public function greaterThanOrEqual($value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::greaterThanOrEqual($value));
    }

    public function between($start, $end): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::between($start, $end));
    }

    // Dates
    public function dateLessThan(\DateTimeInterface $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::dateLessThan($value));
    }

    public function dateLessThanOrEqualTo(\DateTimeInterface $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::dateLessThanOrEqual($value));
    }

    public function dateGreaterThan(\DateTimeInterface $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::dateGreaterThan($value));
    }

    public function dateGreaterThanOrEqual(\DateTimeInterface $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::dateGreaterThanOrEqual($value));
    }

    public function dateBetween(\DateTimeInterface $start, \DateTimeInterface $end): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::dateBetween($start, $end));
    }

    // Arrays
    public function in(array $values): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::isInArray($values));
    }

    public function notIn(array $values): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::not(Comparison::isInArray($values)));
    }

    public function contains(array $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::arrayContains($value));
    }

    public function notContains(array $value): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, Comparison::not(Comparison::arrayContains($value)));
    }

    // Custom
    public function satisfy(callable $callback): QueryBuilder
    {
        return $this->builder->fieldMatchesCriteria($this->currentField, $callback);
    }
}
