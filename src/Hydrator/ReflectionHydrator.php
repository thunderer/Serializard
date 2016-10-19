<?php
namespace Thunder\Serializard\Hydrator;

use Thunder\Serializard\HydratorContainer\HydratorContainerInterface;

final class ReflectionHydrator
{
    public function __invoke(array $data, HydratorContainerInterface $hydrators)
    {
        // FIXME: Refactor hydration to allow generic hydrators
        $ref = new \ReflectionClass($class);
        $object = $ref->newInstanceWithoutConstructor();

        foreach($ref->getProperties() as $property) {
            $property->setAccessible(true);
            $property->setValue($object, $data[$property->getName()]);
        }

        return $object;
    }
}
