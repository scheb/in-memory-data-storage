<?php

namespace Scheb\InMemoryDataStorage\Exception;

class NamedItemNotFoundException extends ItemNotFoundException
{
    public static function createNamedItemNotFoundException(string $name): self
    {
        return new self('Named item "'.$name.'" does not exist in data storage.');
    }
}
