<?php

namespace Scheb\InMemoryDataStorage\Tests\Matching;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\InMemoryDataStorage\Matching\ValueMatcher;
use Scheb\InMemoryDataStorage\Matching\ValueMatchingStrategyInterface;
use Scheb\InMemoryDataStorage\Test\TestCase;

class ValueMatcherTest extends TestCase
{
    private function createValueMatcher(bool $useTypeSensitiveOperator, array $customStrategies): ValueMatcher
    {
        return new ValueMatcher($useTypeSensitiveOperator, $customStrategies);
    }

    private function stubMatchingStrategyReturns(bool $result): MockObject
    {
        $matchingStrategy = $this->createMock(ValueMatchingStrategyInterface::class);
        $matchingStrategy
            ->expects($this->any())
            ->method('match')
            ->willReturn($result);

        return $matchingStrategy;
    }

    /**
     * @test
     */
    public function match_notTypeSensitiveOperatorWithEqualValues_returnTrue(): void
    {
        $valueMatching = $this->createValueMatcher(false, []);
        $result = $valueMatching->match(1, '1');
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function match_notTypeSensitiveOperatorWithDifferentValues_returnFalse(): void
    {
        $valueMatching = $this->createValueMatcher(false, []);
        $result = $valueMatching->match(1, 2);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function match_typeSensitiveOperatorWithIdenticalValues_returnTrue(): void
    {
        $valueMatching = $this->createValueMatcher(true, []);
        $result = $valueMatching->match(1, 1);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function match_typeSensitiveOperatorWithDifferentValues_returnFalse(): void
    {
        $valueMatching = $this->createValueMatcher(true, []);
        $result = $valueMatching->match(1, '1');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function match_customMatchingStrategy_isCalledWithValues(): void
    {
        $matchingStrategy = $this->createMock(ValueMatchingStrategyInterface::class);
        $matchingStrategy
            ->expects($this->once())
            ->method('match')
            ->with(1, 2)
            ->willReturn(false);

        $valueMatching = $this->createValueMatcher(true, [$matchingStrategy]);
        $valueMatching->match(1, 2);
    }

    /**
     * @test
     */
    public function match_customMatchingStrategy_takePreferenceOverNativeComparision(): void
    {
        $matchingStrategy1 = $this->stubMatchingStrategyReturns(false);
        $matchingStrategy2 = $this->stubMatchingStrategyReturns(true);
        $valueMatching = $this->createValueMatcher(true, [$matchingStrategy1, $matchingStrategy2]);

        $result = $valueMatching->match(1, 2);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function match_customComparisionFunctions_fallbackToNativeComparision(): void
    {
        $matchingStrategy = $this->stubMatchingStrategyReturns(false);
        $valueMatching = $this->createValueMatcher(true, [$matchingStrategy]);

        $result = $valueMatching->match(1, 1);
        $this->assertTrue($result);
    }
}
