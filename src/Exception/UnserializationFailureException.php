<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class UnserializationFailureException extends AbstractSerializardException
{
    public static function fromClass($class)
    {
        return new self(sprintf('Class %s does not exist.', $class));
    }
}
