<?php

namespace Scheb\InMemoryDataStorage\PropertyAccess;

use Scheb\InMemoryDataStorage\Test\TestCase;

class ArrayAccessStrategyTest extends TestCase
{
    /**
     * @var ArrayAccessStrategy
     */
    private $accessStrategy;

    protected function setUp()
    {
        $this->accessStrategy = new ArrayAccessStrategy();
    }

    /**
     * @test
     */
    public function supports_supportedValueObject_returnsTrue(): void
    {
        $returnValue = $this->accessStrategy->supports(['key' => 'value']);
        $this->assertTrue($returnValue);
    }

    /**
     * @test
     */
    public function supports_unsupportedValueObject_returnsFalse(): void
    {
        $returnValue = $this->accessStrategy->supports('unsupported');
        $this->assertFalse($returnValue);
    }

    /**
     * @test
     */
    public function getPropertyValue_unsupportedValueObject_throwsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->accessStrategy->getPropertyValue('unsupported', 'property');
    }

    /**
     * @test
     */
    public function getPropertyValue_arrayHasKey_returnValue(): void
    {
        $valueObject = [
            'key' => 'value',
            'property' => 'propertyValue',
        ];

        $returnValue = $this->accessStrategy->getPropertyValue($valueObject, 'property');
        $this->assertEquals('propertyValue', $returnValue);
    }

    /**
     * @test
     */
    public function getPropertyValue_arrayNotHasKey_returnNull(): void
    {
        $valueObject = [
            'key' => 'value',
            'property' => 'propertyValue',
        ];

        $returnValue = $this->accessStrategy->getPropertyValue($valueObject, 'otherProperty');
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function setPropertyValue_anything_returnsFalse(): void
    {
        $valueObject = 'anything';
        $returnValue = $this->accessStrategy->setPropertyValue($valueObject, 'property', 'value');
        $this->assertFalse($returnValue);
    }
}
