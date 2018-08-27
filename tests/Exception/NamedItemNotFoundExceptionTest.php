<?php

namespace Scheb\InMemoryDataStorage\Tests\Exception;

use Scheb\InMemoryDataStorage\Exception\NamedItemNotFoundException;
use Scheb\InMemoryDataStorage\Test\TestCase;

class NamedItemNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function createNamedItemNotFoundException_nameGiven_createExceptionWithMessage(): void
    {
        $exception = NamedItemNotFoundException::createNamedItemNotFoundException('name');
        $this->assertInstanceOf(NamedItemNotFoundException::class, $exception);
        $this->assertEquals('Named item "name" does not exist in data storage.', $exception->getMessage());
    }
}
