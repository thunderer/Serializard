<?php
namespace Thunder\Serializard\Tests\Fake\Interfaces;

final class TypeMultiple implements TypeInterface, AnotherTypeInterface
{
    public function getType()
    {
        return 'typeMultiple';
    }

    public function getValue()
    {
        return 'valueMultiple';
    }

    public function getAlias()
    {
        return 'aliasMultiple';
    }
}
