<?php

namespace Scheb\InMemoryDataStorage\Test\Matching;

class EqualsMatchingDataProvider
{
    public static function provideIdenticalMatchingValues(): array
    {
        $object = new \stdClass();
        $dateTime = new \DateTime('@0');
        $array = [1];

        return [
            'null - null' => [null, null],
            'true - true' => [true, true],
            'false - false' => [false, false],
            'int(0) - int(0)' => [0, 0],
            'int(1) - int(1)' => [1, 1],
            'string() - string()' => ['', ''],
            'string(1) - string(1)' => ['1', '1'],
            'float(1.2) - float(1.2)' => [1.2, 1.2],
            'array(int(1)) - array(int(1))' => [$array, $array],
            'object(1) - object(1)' => [$object, $object],
            'DateTime(@0) - DateTime(@0)' => [$dateTime, $dateTime],
        ];
    }

    public static function provideMatchingButNotIdenticalValues(): array
    {
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $dateTime = new \DateTime('@0');
        $array1 = [1];
        $array2 = ['1'];

        return [
            'null - false' => [null, false],
            'null - int(0)' => [null, 0],
            'null - string()' => [null, ''],
            'false - int(0)' => [false, 0],
            'int(0) - string()' => [0, ''],
            'int(1) - string(1)' => [1, '1'],
            'int(1) - float(1.0)' => [1, 1.0],
            'float(1.2) - string(1.2)' => [1.2, '1.2'],
            'array(int(1)) - array(string(1))' => [$array1, $array2],
            'object(1) - object(2)' => [$object1, $object2],
            'DateTime(@0) - clone DateTime(@0)' => [$dateTime, clone $dateTime],
        ];
    }

    public static function provideDefinitelyDifferentValues(): array
    {
        $object = new \stdClass();
        $dateTime0 = new \DateTime('@0');
        $dateTime1 = new \DateTime('@1');

        return [
            'true - false' => [true, false],
            'int(1) - int(2)' => [1, 2],
            'int(1) - string(2)' => [1, '2'],
            'string(a) - string(b)' => ['a', 'b'],
            'float(1.2) - float(2.4)' => [1.2, 2.4],
            'int(1) - array(int(1))' => [1, [1]],
            'int(0) - object' => [0, $object],
            'string() - object' => ['', $object],
            'string(0) - DateTime' => [0, $dateTime0],
            'DateTime(@0) - DateTime(@1)' => [$dateTime0, $dateTime1],
        ];
    }
}
