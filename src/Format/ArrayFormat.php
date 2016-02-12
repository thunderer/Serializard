<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ArrayFormat extends AbstractFormat implements FormatInterface
{
    public function serialize($var, Normalizers $normalizers)
    {
        return $this->doSerialize($var, $normalizers);
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        if(false === is_array($var)) {
            throw new \RuntimeException('ArrayFormat can unserialize only arrays!');
        }

        return $this->doUnserialize($var, $class, $hydrators);
    }
}
