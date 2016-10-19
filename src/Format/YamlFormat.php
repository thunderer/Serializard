<?php
namespace Thunder\Serializard\Format;

use Symfony\Component\Yaml\Yaml;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class YamlFormat extends AbstractFormat
{
    public function serialize($var, Normalizers $normalizers, NormalizerContextInterface $context)
    {
        return Yaml::dump($this->doSerialize($var, $normalizers, $context), 2, 2);
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        return $this->doUnserialize(Yaml::parse($var), $class, $hydrators);
    }
}
