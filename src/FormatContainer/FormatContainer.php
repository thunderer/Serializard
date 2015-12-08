<?php
namespace Thunder\Serializard\FormatContainer;

use Thunder\Serializard\Format\FormatInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatContainer implements FormatContainerInterface
{
    private $formats = array();

    public function add($class, FormatInterface $handler)
    {
        $this->formats[$class] = $handler;
    }

    public function get($alias)
    {
        return array_key_exists($alias, $this->formats) ? $this->formats[$alias] : null;
    }
}
