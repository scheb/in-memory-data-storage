<?php

require __DIR__.'/../vendor/autoload.php';

$foo = 'I am foo';
$bar = 'I am bar';

$repositoryBuilder = new \Scheb\InMemoryDataStorage\DataRepositoryBuilder();
$repository = $repositoryBuilder
//  ->setDataStorage(new \Scheb\InMemoryDataStorage\DataStorage\ArrayDataStorage())
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
