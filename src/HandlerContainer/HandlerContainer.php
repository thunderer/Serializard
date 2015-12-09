<?php
namespace Thunder\Serializard\HandlerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HandlerContainer implements HandlerContainerInterface
{
    private $handlers = array();
    private $aliases = array();

    public function add($class, $root, $handler)
    {
        if(false === is_callable($handler)) {
            throw new \RuntimeException(sprintf('Invalid handler for class %s!', $class));
        }

        $this->aliases[$class] = $root;
        $this->handlers[$class] = $handler;
    }

    public function addAlias($alias, $class)
    {
        $handler = $this->getHandler($class);

        if(null === $handler) {
            throw new \RuntimeException(sprintf('Handler for class %s does not exist!', $class));
        }

        $this->handlers[$alias] = $handler;
        $this->aliases[$alias] = $this->aliases[$class];
    }

    public function getRoot($class)
    {
        return $this->aliases[$class];
    }

    public function getHandler($class)
    {
        return array_key_exists($class, $this->handlers) ? $this->handlers[$class] : null;
    }
}
