<?php
namespace Thunder\Serializard\FormatContainer;

use Thunder\Serializard\Exception\DuplicateFormatException;
use Thunder\Serializard\Exception\InvalidFormatAliasException;
use Thunder\Serializard\Format\FormatInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatContainer implements FormatContainerInterface
{
    private $formats = [];

    public function add($alias, FormatInterface $handler)
    {
        if(false === \is_string($alias)) {
            throw new InvalidFormatAliasException('Format alias must be a string.');
        }
        if(array_key_exists($alias, $this->formats)) {
            throw new DuplicateFormatException(sprintf('Format with alias `%s` already exists.', $alias));
        }

        $this->formats[$alias] = $handler;
    }

    public function get($alias)
    {
        return array_key_exists($alias, $this->formats) ? $this->formats[$alias] : null;
    }
}
