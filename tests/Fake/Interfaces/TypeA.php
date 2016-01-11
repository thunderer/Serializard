<?php
namespace Thunder\Serializard\Tests\Fake\Interfaces;

final class TypeA implements TypeInterface
{
    public function getType()
    {
        return 'typeA';
    }

    public function getValue()
    {
        return 'valueA';
    }
}
