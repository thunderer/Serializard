<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface FormatInterface
{
    public function serialize($var, Normalizers $normalizers);

    public function unserialize($var, $class, Hydrators $hydrators);
}
