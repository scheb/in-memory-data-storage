<?php

namespace Scheb\InMemoryDataStorage\Test\Matching;

use Scheb\InMemoryDataStorage\Matching\EqualsMatchingStrategy;
use Scheb\InMemoryDataStorage\Test\TestCase;

class EqualsMatchingStrategyTest extends TestCase
{
    /**
     * @var EqualsMatchingStrategy
     */
    private $matchingStrategy;

    public function setUp()
    {
        $this->matchingStrategy = new EqualsMatchingStrategy();
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
    public function match_noneIdenticalValues_valuesMatch($value1, $value2): void
    {
        $result = $this->matchingStrategy->match($value1, $value2);
        $this->assertTrue($result);
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
