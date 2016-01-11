<?php
namespace Thunder\Serializard\Tests\Fake\Interfaces;

final class TypeB implements TypeInterface
{
    public function getType()
    {
        return 'typeB';
    }

    public function getValue()
    {
        return 'valueB';
    }
}
