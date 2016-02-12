<?php
namespace Thunder\Serializard\Format;

use Symfony\Component\Yaml\Yaml;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class YamlFormat extends AbstractFormat implements FormatInterface
{
    public function serialize($var, Normalizers $normalizers)
    {
        return Yaml::dump($this->doSerialize($var, $normalizers), 2, 2);
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        return $this->doUnserialize(Yaml::parse($var), $class, $hydrators);
    }
}
