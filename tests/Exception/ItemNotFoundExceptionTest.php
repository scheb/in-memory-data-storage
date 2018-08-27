<?php

namespace Scheb\InMemoryDataStorage\Tests\Exception;

use Scheb\InMemoryDataStorage\Exception\ItemNotFoundException;
use Scheb\InMemoryDataStorage\Test\TestCase;

class ItemNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function createItemNotFoundException_withScalar_createExceptionWithMessage(): void
    {
        $exception = ItemNotFoundException::createItemNotFoundException(123);
        $this->assertInstanceOf(ItemNotFoundException::class, $exception);
        $this->assertEquals('Item "123" does not exist in data storage.', $exception->getMessage());
    }

    /**
     * @test
     */
    public function createItemNotFoundException_withObject_createExceptionWithMessage(): void
    {
        $object = new \stdClass();
        $exception = ItemNotFoundException::createItemNotFoundException($object);
        $this->assertInstanceOf(ItemNotFoundException::class, $exception);
        $this->assertRegExp('#^Item "[0-9a-f]{32}" does not exist in data storage\\.$#', $exception->getMessage());
    }
}
