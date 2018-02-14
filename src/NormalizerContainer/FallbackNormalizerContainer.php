<?php
namespace Thunder\Serializard\NormalizerContainer;

use Thunder\Serializard\Exception\InvalidClassNameException;
use Thunder\Serializard\Exception\InvalidNormalizerException;
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

    public function add($class, $handler)
    {
        if(false === is_callable($handler)) {
            throw new InvalidNormalizerException(sprintf('Invalid handler for class %s!', $class));
        }

        if(class_exists($class)) {
            $this->aliases[$class] = $class;
            $this->handlers[$class] = $handler;
        } elseif(interface_exists($class)) {
            $this->aliases[$class] = $class;
            $this->interfaces[$class] = $handler;
        } else {
            throw new InvalidClassNameException(sprintf('Given value %s is neither class nor interface name!', $class));
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
        if(array_key_exists($class, $this->handlers)) {
            return $this->handlers[$class];
        }

        $parents = array_intersect(array_keys($this->handlers), class_parents($class));
        if($parents) {
            return $this->handlers[array_pop($parents)];
        }

        $interfaces = array_intersect(array_keys($this->interfaces), array_values(class_implements($class)));
        if($interfaces) {
            if(count($interfaces) > 1) {
                throw new NormalizerConflictException(sprintf('Class %s implements interfaces with colliding handlers!', $class));
            }

            return $this->interfaces[array_shift($interfaces)];
        }

        return $this->default;
    }

    public function setDefault($handler)
    {
        if(false === is_callable($handler)) {
            throw new InvalidNormalizerException('Default normalizer handler must be callable!');
        }

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
