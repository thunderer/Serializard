<?php
namespace Thunder\Serializard\Exception;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatNotFoundException extends AbstractSerializardException
{
    public static function fromAlias($alias)
    {
        return new self(sprintf('No registered format for alias %s.', $alias));
    }
}
