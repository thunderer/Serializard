<?php
namespace Thunder\Serializard\HandlerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HandlerContainer implements  HandlerContainerInterface
{
    private $handlers = array();
    private $aliases = array();

    public function add($class, $alias, $handler)
    {
        $this->aliases[$class] = $alias;
        $this->handlers[$class] = $handler;
    }

    public function getAlias($class)
    {
        return $this->aliases[$class];
    }

    public function getHandler($class)
    {
        return $this->handlers[$class];
    }
}
