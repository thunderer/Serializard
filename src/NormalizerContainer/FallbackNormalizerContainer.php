<?php
namespace Thunder\Serializard\NormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FallbackNormalizerContainer implements NormalizerContainerInterface
{
    private $handlers = array();
    private $interfaces = array();
    private $aliases = array();

    public function add($class, $handler)
    {
        if(false === is_callable($handler)) {
            throw new \RuntimeException(sprintf('Invalid handler for class %s!', $class));
        }

        if(class_exists($class)) {
            $this->aliases[$class] = $class;
            $this->handlers[$class] = $handler;
        } elseif(interface_exists($class)) {
            $this->aliases[$class] = $class;
            $this->interfaces[$class] = $handler;
        } else {
            throw new \RuntimeException(sprintf('Given value %s is neither class nor interface name!', $class));
        }
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

    public function getHandler($class)
    {
        if(isset($this->handlers[$class])) {
            return $this->handlers[$class];
        }

        foreach(class_parents($class) as $ancestor) {
            if(isset($this->handlers[$ancestor])) {
                return $this->handlers[$ancestor];
            }
        }

        foreach(class_implements($class) as $interface) {
            if(isset($this->interfaces[$interface])) {
                return $this->interfaces[$interface];
            }
        }

        return null;
    }
}
