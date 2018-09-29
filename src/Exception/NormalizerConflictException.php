<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class NormalizerConflictException extends AbstractSerializardException
{
    public static function fromClass($class, array $interfaces)
    {
        return new self(sprintf('Class %s implements interfaces with colliding handlers: %s.', $class, implode(', ', $interfaces)));
    }
}
