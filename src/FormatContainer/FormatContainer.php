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

    public function get($class)
    {
        return $this->formats[$class];
    }
}
