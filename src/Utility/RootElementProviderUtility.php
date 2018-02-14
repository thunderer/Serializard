<?php
namespace Thunder\Serializard\Utility;

use Thunder\Serializard\Exception\InvalidClassNameException;

final class RootElementProviderUtility
{
    private $aliases;

    public function __construct(array $aliases)
    {
        foreach($aliases as $key => $alias) {
            if(false === is_string($key)) {
                throw new InvalidClassNameException('Invalid alias class name, string required.');
            }
            if(false === is_string($alias)) {
                throw new \InvalidArgumentException(sprintf('Invalid alias for class %s, string required.', $key));
            }
        }

        $this->aliases = $aliases;
    }

    public function __invoke($class)
    {
        if(false === array_key_exists($class, $this->aliases)) {
            throw new \RuntimeException(sprintf('Root element alias for class %s not found.', $class));
        }

        return $this->aliases[$class];
    }
}
