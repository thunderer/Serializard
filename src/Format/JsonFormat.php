<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class JsonFormat extends AbstractFormat implements FormatInterface
{
    public function serialize($var, Normalizers $normalizers)
    {
        return json_encode($this->doSerialize($var, $normalizers));
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        return $this->doUnserialize(json_decode($var, true), $class, $hydrators);
    }
}
