<?php

namespace Scheb\InMemoryDataStorage\Repository;

use Scheb\InMemoryDataStorage\Comparison;

function compare(): Comparison
{
    return new Comparison();
}
