<?php
namespace Thunder\Serializard\HydratorContainer;

use Thunder\Serializard\Exception\HydratorConflictException;
use Thunder\Serializard\Exception\HydratorNotFoundException;
use Thunder\Serializard\Exception\ClassNotFoundException;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FallbackHydratorContainer implements HydratorContainerInterface
{
    private $handlers = [];
    private $interfaces = [];
    private $aliases = [];

    public function add($class, callable $handler)
    {
        if(class_exists($class)) {
            $this->aliases[$class] = $class;
            $this->handlers[$class] = $handler;
        } elseif(interface_exists($class)) {
            $this->aliases[$class] = $class;
            $this->interfaces[$class] = $handler;
        } else {
            throw ClassNotFoundException::fromClass($class);
        }
    }

    public function addAlias($alias, $class)
    {
        $handler = $this->getHandler($class);

        $this->handlers[$alias] = $handler;
        $this->aliases[$alias] = $this->aliases[$class];
    }

    public function getHandler($class)
    {
        if(array_key_exists($class, $this->handlers)) {
            return $this->handlers[$class];
        }

        $parents = array_intersect(array_keys($this->handlers), class_parents($class));
        if($parents) {
            return $this->handlers[array_pop($parents)];
        }

        $interfaces = array_intersect(array_keys($this->interfaces), array_values(class_implements($class)));
        if($interfaces) {
            if(\count($interfaces) > 1) {
                throw HydratorConflictException::fromClass($class, $interfaces);
            }

            return $this->interfaces[array_shift($interfaces)];
        }

        throw HydratorNotFoundException::fromClass($class);
    }

    public function hydrate($class, array $data)
    {
        return \call_user_func($this->getHandler($class), $data, $this);
    }
}
