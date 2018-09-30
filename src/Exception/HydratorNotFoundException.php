<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorNotFoundException extends AbstractSerializardException
{
    public static function fromClass($class)
    {
        return new self(sprintf('No hydrator for class %s.', $class));
    }
}
