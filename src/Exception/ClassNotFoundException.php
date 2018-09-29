<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ClassNotFoundException extends AbstractSerializardException
{
    public static function fromClass($class)
    {
        return new self(sprintf('Given value %s is neither class nor interface name!', $class));
    }
}
