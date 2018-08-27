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
$storage = new \Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage();
$repository = new \Scheb\InMemoryDataStorage\Repository\DataRepository($storage);

// Simple CRUD
$repository->addItem($foo);
$repository->containsItem($foo); // returns true
$repository->getAllItems(); // returns [$foo]
$repository->removeItem($foo);

// Named items
$repository->addNamedItem('foo', $foo);
$repository->namedItemExists('foo'); // returns true
$repository->getNamedItem('foo'); // returns $foo
$repository->replaceNamedItem('foo', $bar);
$repository->getNamedItem('foo'); // returns $bar
$repository->removeNamedItem('foo');
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
