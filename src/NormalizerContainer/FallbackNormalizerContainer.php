<?php
namespace Thunder\Serializard\NormalizerContainer;

use Thunder\Serializard\Exception\ClassNotFoundException;
use Thunder\Serializard\Exception\NormalizerConflictException;
use Thunder\Serializard\Exception\NormalizerNotFoundException;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FallbackNormalizerContainer implements NormalizerContainerInterface
{
    private $default;
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
            throw new ClassNotFoundException(sprintf('Given value %s is neither class nor interface name!', $class));
        }
    }

    public function addAlias($alias, $class)
    {
        $handler = $this->getHandler($class);

        if(null === $handler) {
            throw new NormalizerNotFoundException(sprintf('Handler for class %s does not exist!', $class));
        }

        $this->handlers[$alias] = $handler;
        $this->aliases[$alias] = $this->aliases[$class];
    }

    public function getHandler($class)
    {
        if(isset($this->handlers[$class])) {
            return $this->handlers[$class];
        }

        $parents = array_intersect(array_keys($this->handlers), class_parents($class));
        if($parents) {
            return $this->handlers[array_pop($parents)];
        }

        $interfaces = array_intersect(array_keys($this->interfaces), array_values(class_implements($class)));
        if($interfaces) {
            if(\count($interfaces) > 1) {
                throw new NormalizerConflictException(sprintf('Class %s implements interfaces with colliding handlers!', $class));
            }

            return $this->interfaces[array_shift($interfaces)];
        }

        return $this->default;
    }

    public function setDefault(callable $handler)
    {
        $this->default = $handler;
    }

    public function hasDefault()
    {
        return null !== $this->default;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
