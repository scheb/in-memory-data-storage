<?php

namespace Scheb\InMemoryDataStorage\Test\Matching;

use Scheb\InMemoryDataStorage\Matching\CallbackMatchingStrategy;
use Scheb\InMemoryDataStorage\Test\TestCase;

class CallbackMatchingStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function match_customComparisionFunction_isCalledWithValues(): void
    {
        $comparisonFunctionArguments = null;
        $comparisonFunction = function ($value1, $value2) use (&$comparisonFunctionArguments): bool {
            $comparisonFunctionArguments = func_get_args();

            return false;
        };

        $matchingStrategy = new CallbackMatchingStrategy($comparisonFunction);
        $matchingStrategy->match(1, 2);

        $this->assertNotNull($comparisonFunctionArguments);
        $this->assertEquals([1, 2], $comparisonFunctionArguments);
    }

    /**
     * @test
     * @dataProvider provideCallbackReturnValues
     */
    public function match_customComparisionFunction_returnResultOfComparisionFunction(bool $functionReturnValue): void
    {
        $comparisonFunction = function () use ($functionReturnValue): bool {
            return $functionReturnValue;
        };

        $matchingStrategy = new CallbackMatchingStrategy($comparisonFunction);
        $result = $matchingStrategy->match(1, 2);
        $this->assertEquals($functionReturnValue, $result);
    }

    public function provideCallbackReturnValues(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
