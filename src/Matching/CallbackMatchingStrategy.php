<?php

namespace Scheb\InMemoryDataStorage\Matching;

class CallbackMatchingStrategy implements ValueMatchingStrategyInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function match($value1, $value2): bool
    {
        return call_user_func($this->callback, $value1, $value2);
    }
}
