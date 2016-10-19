<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface FormatInterface
{
    public function serialize($var, Normalizers $normalizers, NormalizerContextInterface $context);

    public function unserialize($var, $class, Hydrators $hydrators);
}
