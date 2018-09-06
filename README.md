scheb/in-memory-data-storage
============================

*** **[WIP]** This library is currently work in progress. Public API might change any time. ***

[![Build Status](https://travis-ci.org/scheb/in-memory-data-storage.svg?branch=master)](https://travis-ci.org/scheb/in-memory-data-storage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scheb/in-memory-data-storage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scheb/in-memory-data-storage/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/scheb/in-memory-data-storage/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/scheb/in-memory-data-storage/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/scheb/in-memory-data-storage/v/stable.svg)](https://packagist.org/packages/scheb/in-memory-data-storage)
[![License](https://poser.pugx.org/scheb/in-memory-data-storage/license.svg)](https://packagist.org/packages/scheb/in-memory-data-storage)

A fast in-memory data storage in plain PHP. It can be used as a test double for a database, in order to to decouple the
test cases from the database and speeding them up.

Features
--------

- CRUD operations
- Convenience methods for data selection and manipulation
- Named items

Installation
------------

```bash
composer require scheb/in-memory-data-storage
```

How to use
----------

You can find an executable example in the `doc` folder.

```php
<?php

require __DIR__.'/../vendor/autoload.php';

use Scheb\InMemoryDataStorage\DataRepositoryBuilder;
use Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage;
use function \Scheb\InMemoryDataStorage\Repository\compare;

$foo = 'I am foo';
$bar = 'I am bar';

$repositoryBuilder = new DataRepositoryBuilder();
$repository = $repositoryBuilder
//  ->setDataStorage(new ArrayDataStorage())
    ->build();


// Simple CRUD
$repository->addItem($foo);
$repository->containsItem($foo); // returns true
$repository->getAllItems(); // returns [$foo]
$repository->removeItem($foo);

// Named items
$repository->setNamedItem('foo', $foo);
$repository->namedItemExists('foo'); // returns true
$repository->getNamedItem('foo'); // returns $foo
$repository->replaceNamedItem('foo', $bar);
$repository->getNamedItem('foo'); // returns $bar
$repository->removeNamedItem('foo');


// Advanced get
$repository->getAllItemsByCriteria(['property' => 'value']);
// $repository->getOneItemByCriteria(...); // The same, but only one item is retrieved

// Advanced update
$repository->updateAllItemsByCriteria(
    ['property' => 'value'], // Match criteria
    ['property' => 'newValue', 'otherProperty' => 42] // Property updates
);
// $repository->updateOneByCriteria(...); // The same, but only one item is updated

// Advanced remove
$repository->removeAllItemsByCriteria(['property' => 'value']);
// $repository->removeOneItemByCriteria(...); // The same, but only one item is removed


// Comparision functions in matching criteria
$repository->getAllItemsByCriteria(['property' => compare()->notNull()]);
$repository->getAllItemsByCriteria(['property' => compare()->lessThan(3)]);
$repository->getAllItemsByCriteria(['property' => compare()->between(1, 3)]);
$repository->getAllItemsByCriteria(['property' => compare()->isInArray([1, 2, 3])]);
$repository->getAllItemsByCriteria(['property' => compare()->arrayContains('arrayElement')]);
$repository->getAllItemsByCriteria(['property' => compare()->dateGreaterThan(new \DateTime('2018-01-01'))]);
// ... and many more, see Scheb\InMemoryDataStorage\Comparison
```

How to extend
-------------

Be default, the library comes with a simple array-based storage, but you exchange the data storage engine with whatever
you like, by implementing `\Scheb\InMemoryDataStorage\DataStorage\DataStorageInterface`.

Contribute
----------
You're welcome to [contribute](https://github.com/scheb/in-memory-data-storage/graphs/contributors) to this library by
creating a pull requests or feature request in the issues section. For pull requests, please follow these guidelines:

- Symfony code style
- PHP7.1 type hints for everything (including: return types, `void`, nullable types)
- Please add/update test cases
- Test methods should be named `[method]_[scenario]_[expected result]`

To run the test suite install the dependencies with `composer install` and then execute `bin/phpunit`.

License
-------
This bundle is available under the [MIT license](LICENSE).
