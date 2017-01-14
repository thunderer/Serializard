<?php
namespace Thunder\Serializard\FormatContainer;

use Thunder\Serializard\Format\FormatInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatContainer implements FormatContainerInterface
{
    private $formats = array();

    public function add($alias, FormatInterface $handler)
    {
        $this->formats[$alias] = $handler;
    }

    public function get($alias)
    {
        return array_key_exists($alias, $this->formats) ? $this->formats[$alias] : null;
    }
}
