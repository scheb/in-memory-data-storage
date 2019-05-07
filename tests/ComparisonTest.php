<?php

namespace Scheb\InMemoryDataStorage\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\InMemoryDataStorage\Comparison;
use Scheb\InMemoryDataStorage\Matching\ValueMatcherInterface;
use function Scheb\InMemoryDataStorage\Repository\compare;

class ComparisonTest extends TestCase
{
    /**
     * @var MockObject|ValueMatcherInterface
     */
    private $valueMatcher;

    private function createValueMatcher(): MockObject
    {
        return $this->valueMatcher = $this->createMock(ValueMatcherInterface::class);
    }

    private function assertValueMatcherMatches(array $valuePairs, array $results): void
    {
        $this->valueMatcher
            ->expects($this->exactly(count($valuePairs)))
            ->method('match')
            ->withConsecutive(...$valuePairs)
            ->willReturnOnConsecutiveCalls(...$results);
    }

    /**
     * @test
     */
    public function not_passComparisionFunction_callComparisionFunctionWithArguments(): void
    {
        $this->createValueMatcher();

        $internalCallbackArgs = [];
        $innerCallback = function () use (&$internalCallbackArgs) {
            $internalCallbackArgs = func_get_args();
        };
        $callback = Comparison::not($innerCallback);
        $callback(123, $this->valueMatcher);

        $this->assertEquals(123, $internalCallbackArgs[0] ?? null);
        $this->assertEquals($this->valueMatcher, $internalCallbackArgs[1] ?? null);
    }

    /**
     * @test
     * @dataProvider provideNotValues
     */
    public function not_innerCallbackEvaluates_returnReverseBoolean(bool $innerResult, bool $expectedResult): void
    {
        $this->createValueMatcher();

        $innerCallback = function () use ($innerResult) {
            return $innerResult;
        };
        $callback = Comparison::not($innerCallback);
        $result = $callback(123, $this->valueMatcher);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideNotValues(): array
    {
        return [
            [true, false],
            [false, true],
        ];
    }

    /**
     * @test
     * @dataProvider provideIsNullValues
     */
    public function isNull_withValue_returnResult($value, bool $result): void
    {
        $callback = Comparison::isNull();
        $this->assertEquals($result, $callback($value));
    }

    public function provideIsNullValues(): array
    {
        return [
            [0, false],
            ['', false],
            [new \stdClass(), false],
            [[], false],
            [null, true],
        ];
    }

    /**
     * @test
     * @dataProvider provideNotNullValues
     */
    public function notNull_withValue_returnResult($value, bool $result): void
    {
        $this->createValueMatcher();
        $callback = Comparison::notNull();
        $this->assertEquals($result, $callback($value, $this->valueMatcher));
    }

    public function provideNotNullValues(): array
    {
        return [
            [0, true],
            ['', true],
            [new \stdClass(), true],
            [[], true],
            [null, false],
        ];
    }

    /**
     * @test
     */
    public function compareFunction_whenCalled_returnsNewInstance(): void
    {
        $returnValue = compare();
        $this->assertInstanceOf(Comparison::class, $returnValue);
    }

    /**
     * @test
     */
    public function equals_sameValue_returnTrue(): void
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches([[1, 1]], [true]);

        $callback = Comparison::equals(1);
        $this->assertTrue($callback(1, $this->valueMatcher));
    }

    /**
     * @test
     */
    public function equals_differentValue_returnFalse(): void
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches([[2, 1]], [false]);

        $callback = Comparison::equals(1);
        $this->assertFalse($callback(2, $this->valueMatcher));
    }

    /**
     * @test
     * @dataProvider provideBetweenValues
     */
    public function between_compareAgainst2And3_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::between(2, 3);
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideBetweenValues(): array
    {
        return [
            [1.9999, false],
            [2, true],
            [3, true],
            [3.0001, false],
            ['NaN', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideGreaterThanValues
     */
    public function greaterThan_compareAgainst3_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::greaterThan(3);
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideGreaterThanValues(): array
    {
        return [
            [4, true],
            [3, false],
            [2, false],
            ['NaN', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideGreaterThanOrEqualValues
     */
    public function greaterThanOrEqual_compareAgainst3_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::greaterThanOrEqual(3);
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideGreaterThanOrEqualValues(): array
    {
        return [
            [4, true],
            [3, true],
            [2, false],
            ['NaN', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideLessThanValues
     */
    public function lessThan_compareAgainst3_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::lessThan(3);
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideLessThanValues(): array
    {
        return [
            [4, false],
            [3, false],
            [2, true],
            ['NaN', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideLessThanOrEqualValues
     */
    public function lessThanOrEqual_compareAgainst3_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::lessThanOrEqual(3);
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideLessThanOrEqualValues(): array
    {
        return [
            [4, false],
            [3, true],
            [2, true],
            ['NaN', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDateBetweenValues
     */
    public function dateBetween_compareAgainst2000And2001_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::dateBetween(new \DateTime('2000-01-01 00:00:00.000'), new \DateTime('2001-01-01 00:00:00.000'));
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideDateBetweenValues(): array
    {
        return [
            [new \DateTime('1999-12-31 23:59:59.999'), false],
            [new \DateTime('2000-01-01 00:00:00.000'), true],
            [new \DateTime('2001-01-01 00:00:00.000'), true],
            [new \DateTime('2001-01-01 00:00:00.001'), false],
            ['NaD', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDateGreaterThanValues
     */
    public function dateGreaterThan_compareAgainst2000_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::dateGreaterThan(new \DateTime('2000-01-01 00:00:00.000'));
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideDateGreaterThanValues(): array
    {
        return [
            [new \DateTime('1999-12-31 23:59:59.999'), false],
            [new \DateTime('2000-01-01 00:00:00.000'), false],
            [new \DateTime('2000-01-01 00:00:00.001'), true],
            ['NaD', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDateGreaterThanOrEqualValues
     */
    public function dateGreaterThanOrEqual_compareAgainst2000_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::dateGreaterThanOrEqual(new \DateTime('2000-01-01 00:00:00.000'));
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideDateGreaterThanOrEqualValues(): array
    {
        return [
            [new \DateTime('1999-12-31 23:59:59.999'), false],
            [new \DateTime('2000-01-01 00:00:00.000'), true],
            [new \DateTime('2000-01-01 00:00:00.001'), true],
            ['NaD', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDateLessThanValues
     */
    public function dateLessThan_compareAgainst2000_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::dateLessThan(new \DateTime('2000-01-01 00:00:00.000'));
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideDateLessThanValues(): array
    {
        return [
            [new \DateTime('1999-12-31 23:59:59.999'), true],
            [new \DateTime('2000-01-01 00:00:00.000'), false],
            [new \DateTime('2000-01-01 00:00:00.001'), false],
            ['NaD', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDateLessThanOrEqualValues
     */
    public function dateLessThanOrEqual_compareAgainst2000_returnResult($givenValue, bool $result): void
    {
        $callback = Comparison::dateLessThanOrEqual(new \DateTime('2000-01-01 00:00:00.000'));
        $this->assertEquals($result, $callback($givenValue));
    }

    public function provideDateLessThanOrEqualValues(): array
    {
        return [
            [new \DateTime('1999-12-31 23:59:59.999'), true],
            [new \DateTime('2000-01-01 00:00:00.000'), true],
            [new \DateTime('2000-01-01 00:00:00.001'), false],
            ['NaD', false],
        ];
    }

    /**
     * @test
     */
    public function isInArray_valueNotMatches_returnTrue(): void
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches(
            [[2, 1], [2, 2]],
            [false, true]
        );

        $callback = Comparison::isInArray([1, 2]);
        $this->assertTrue($callback(2, $this->valueMatcher));
    }

    /**
     * @test
     */
    public function isInArray_valueMatches_returnFalse(): void
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches(
            [[3, 1], [3, 2]],
            [false, false]
        );

        $callback = Comparison::isInArray([1, 2]);
        $this->assertFalse($callback(3, $this->valueMatcher));
    }

    /**
     * @test
     */
    public function arrayContains_noMatchingValue_returnFalse()
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches(
            [[1, 3], [2, 3]],
            [false, false]
        );

        $callback = Comparison::arrayContains(3);
        $this->assertFalse($callback([1, 2], $this->valueMatcher));
    }

    /**
     * @test
     */
    public function arrayContains_hasMatchingValue_returnTrue()
    {
        $this->createValueMatcher();
        $this->assertValueMatcherMatches(
            [[1, 2], [2, 2]],
            [false, true]
        );

        $callback = Comparison::arrayContains(2);
        $this->assertTrue($callback([1, 2], $this->valueMatcher));
    }
}
