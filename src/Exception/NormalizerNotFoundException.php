<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class NormalizerNotFoundException extends AbstractSerializardException
{
    public static function fromClass($class)
    {
        return new self(sprintf('Missing normalizer for class %s.', $class));
    }
}
