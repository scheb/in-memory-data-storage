<?php

namespace Scheb\InMemoryDataStorage\Test\Matching;

use Scheb\Comparator\ComparatorInterface;
use Scheb\InMemoryDataStorage\Matching\ValueMatcher;
use Scheb\InMemoryDataStorage\Test\TestCase;

class ValueMatcherTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideResultsForComparator
     */
    public function match_valuesGiven_returnResultFromComparator(bool $comparatorResult): void
    {
        $comparator = $this->createMock(ComparatorInterface::class);
        $comparator
            ->expects($this->once())
            ->method('isEqual')
            ->with(1, 2)
            ->willReturn($comparatorResult);

        $valueMatcher = new ValueMatcher($comparator);
        $returnValue = $valueMatcher->match(1, 2);

        $this->assertEquals($comparatorResult, $returnValue);
    }

    public function provideResultsForComparator(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
