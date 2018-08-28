<?php

namespace Scheb\InMemoryDataStorage\Test\Matching;

use Scheb\InMemoryDataStorage\Matching\TypeSensitiveEqualsMatchingStrategy;
use Scheb\InMemoryDataStorage\Test\TestCase;

class TypeSensitiveEqualsMatchingStrategyTest extends TestCase
{
    /**
     * @var TypeSensitiveEqualsMatchingStrategy
     */
    private $matchingStrategy;

    protected function setUp()
    {
        $this->matchingStrategy = new TypeSensitiveEqualsMatchingStrategy();
    }

    /**
     * @test
     * @dataProvider \Scheb\InMemoryDataStorage\Test\Matching\EqualsMatchingDataProvider::provideIdenticalMatchingValues
     */
    public function match_identicalValues_valuesMatch($value1, $value2): void
    {
        $result = $this->matchingStrategy->match($value1, $value2);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider \Scheb\InMemoryDataStorage\Test\Matching\EqualsMatchingDataProvider::provideMatchingButNotIdenticalValues
     */
    public function match_noneIdenticalValues_valuesNotMatch($value1, $value2): void
    {
        $result = $this->matchingStrategy->match($value1, $value2);
        $this->assertFalse($result);
    }

    /**
     * @test
     * @dataProvider \Scheb\InMemoryDataStorage\Test\Matching\EqualsMatchingDataProvider::provideDefinitelyDifferentValues
     */
    public function match_differentValues_valuesNotMatch($value1, $value2): void
    {
        $result = $this->matchingStrategy->match($value1, $value2);
        $this->assertFalse($result);
    }
}
