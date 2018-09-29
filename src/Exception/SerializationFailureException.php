<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class SerializationFailureException extends AbstractSerializardException
{
    public static function fromCycle(array $classes)
    {
        return new self('Nesting cycle: '.implode(' -> ', $classes));
    }

    public static function fromJson($msg)
    {
        return new self(sprintf('JSON failure: `%s`.', $msg));
    }
}
