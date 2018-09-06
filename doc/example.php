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
