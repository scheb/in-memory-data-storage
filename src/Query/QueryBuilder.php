<?php

declare(strict_types=1);

namespace Scheb\InMemoryDataStorage\Query;

use Scheb\InMemoryDataStorage\DataRepository;
use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;

class QueryBuilder
{

    /**
     * @var DataRepository
     */
    private $storage;

    /**
     * @var Query
     */
    private $query;

    public function __construct(DataRepository $storage)
    {
        $this->storage = $storage;
        $this->query = new Query();
    }

    /**
     * @param string $identifier
     * @return self
     */
    public function nameIs($identifier): self
    {
        $this->query->setName($identifier);
        return $this;
    }

    /**
     * @param callable $callback
     * @return self
     */
    public function where(callable $callback): self
    {
        $this->query->setCriteriaCallback($callback);
        return $this;
    }

    /**
     * Set the current field to operate on.
     *
     * @param string $field
     * @return FieldQueryBuilder
     */
    public function field($field): FieldQueryBuilder
    {
        return new FieldQueryBuilder($this, $field);
    }

    /**
     * Set the limit for the query.
     *
     * This is only relevant for find queries.
     *
     * @param integer $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->query->setLimit($limit);
        return $this;
    }

    /**
     * Set the skip for the query cursor.
     *
     * This is only relevant for find queries, or mapReduce queries that store
     * results in an output collection and return a cursor.
     *
     * @param integer $skip
     * @return self
     */
    public function skip(int $skip): self
    {
        $this->query->setOffset($skip);
        return $this;
    }

    /**
     * Set one or more field/order pairs on which to sort the query.
     *
     * If sorting by multiple fields, the first argument should be an array of
     * field name (key) and order (value) pairs.
     *
     * @param array|string $fieldName Field name or array of field/order pairs
     * @param int|string $order Field order (if one field is specified)
     * @return self
     */
    public function sort(string $fieldName, int $order = 1): self
    {
        $fields = is_array($fieldName) ? $fieldName : [$fieldName => $order];
        foreach ($fields as $fieldName => $order) {
            if (is_string($order)) {
                $order = strtolower($order) === 'asc' ? 1 : -1;
            }
            $this->query->setFieldSort($fieldName, $order);
        }
        return $this;
    }

    public function fieldMatchesCriteria(string $fieldName, callable $callback): self
    {
        $this->query->setFieldCriteria($fieldName, $callback);
        return $this;
    }

// Needed?
//    public function replaceWith($entity): void
//    {
//        $this->storage->replace($this->query, $entity);
//    }
//
//    public function insertOrReplaceWith($entity): void
//    {
//        $this->storage->insertOrReplace($this->query, $entity);
//    }

    public function getAll(string $keyFieldName = null): array
    {
        return $this->storage->queryAll($this->query, $keyFieldName);
    }

    public function getOne()
    {
        return $this->storage->queryOne($this->query);
    }

    public function getOneOrReturnNull()
    {
        try {
            return $this->storage->queryOne($this->query);
        } catch (ItemNotFoundException $ex) {
            return null;
        }
    }

    public function update(callable $callback): void
    {
        $this->storage->queryUpdate($this->query, $callback);
    }

    public function remove(): void
    {
        $this->storage->queryRemove($this->query);
    }

    public function count(): int
    {
        $items = $this->storage->queryAll($this->query);

        return count($items);
    }
}
