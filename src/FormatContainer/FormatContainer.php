<?php
namespace Thunder\Serializard\FormatContainer;

use Thunder\Serializard\Exception\FormatNotFoundException;
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
            throw new \InvalidArgumentException('Format alias must be a string.');
        }
        if(array_key_exists($alias, $this->formats)) {
            throw new \InvalidArgumentException(sprintf('Format with alias `%s` already exists.', $alias));
        }

        $this->formats[$alias] = $handler;
    }

    public function get($alias)
    {
        if(false === array_key_exists($alias, $this->formats)) {
            throw FormatNotFoundException::fromAlias($alias);
        }

        return $this->formats[$alias];
    }
}
