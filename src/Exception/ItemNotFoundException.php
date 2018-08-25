<?php

namespace Scheb\InMemoryDataStorage\Exception;

class ItemNotFoundException extends \Exception
{
    public static function createItemNotFoundException($item): self
    {
        $identity = is_object($item) ? spl_object_hash($item) : (string) $item;

        return new self('Item "'.$identity.'" does not exist in data storage.');
    }
}
