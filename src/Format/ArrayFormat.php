<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\Exception\UnserializationFailureException;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ArrayFormat extends AbstractFormat
{
    public function serialize($var, Normalizers $normalizers, NormalizerContextInterface $context)
    {
        return $this->doSerialize($var, $normalizers, $context);
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        if(false === \is_array($var)) {
            throw new UnserializationFailureException('ArrayFormat can unserialize only arrays!');
        }

        return $this->doUnserialize($var, $class, $hydrators);
    }
}
