<?php
namespace Thunder\Serializard\HydratorContainer;
use Thunder\Serializard\Exception\HydratorNotFoundException;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface HydratorContainerInterface
{
    /**
     * @param string $class Class name
     *
     * @return callable
     *
     * @throws HydratorNotFoundException
     */
    public function getHandler($class);
}
