<?php
namespace Thunder\Serializard\Hydrator;

use Thunder\Serializard\Exception\InvalidClassNameException;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface;

final class ReflectionHydrator
{
    private $class;
    private $objects;

    public function __construct($class, array $objects)
    {
        if(false === class_exists($class)) {
            throw new InvalidClassNameException(sprintf('Unknown hydration class %s!', $class));
        }

        $this->class = $class;
        $this->objects = $objects;
    }

    public function __invoke(array $data, HydratorContainerInterface $hydrators)
    {
        $ref = new \ReflectionClass($this->class);
        $object = $ref->newInstanceWithoutConstructor();

        foreach($ref->getProperties() as $property) {
            $name = $property->getName();
            if(false === array_key_exists($name, $data)) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($object, $this->computeValue($name, $data[$name], $hydrators));
        }

        return $object;
    }

    private function computeValue($name, $data, HydratorContainerInterface $hydrators)
    {
        if(false === array_key_exists($name, $this->objects)) {
            return $data;
        }

        $type = $this->objects[$name];
        if('[]' !== substr($type, -2)) {
            return call_user_func($hydrators->getHandler($this->objects[$name]), $data, $hydrators);
        }

        $type = substr($type, 0, -2);
        $items = [];
        foreach($data as $item) {
            $items[] = call_user_func($hydrators->getHandler($type), $item, $hydrators);
        }

        return $items;
    }
}
