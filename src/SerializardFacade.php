<?php
namespace Thunder\Serializard;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\FormatInterface;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\Format\XmlFormat;
use Thunder\Serializard\Format\YamlFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\NormalizerContext\ParentNormalizerContext;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class SerializardFacade
{
    /** @var FallbackNormalizerContainer */
    private $normalizers;
    /** @var FallbackHydratorContainer */
    private $hydrators;
    /** @var FormatContainer */
    private $formats;

    public function __construct()
    {
        $this->normalizers = new FallbackNormalizerContainer();
        $this->hydrators = new FallbackHydratorContainer();

        $formats = new FormatContainer();
        $formats->add('json', new JsonFormat());
        $formats->add('array', new ArrayFormat());
        $formats->add('yaml', new YamlFormat());
        $formats->add('xml', new XmlFormat());
        $this->formats = $formats;
    }

    public function addFormat($alias, FormatInterface $format)
    {
        $this->formats->add($alias, $format);
    }

    public function addNormalizer($class, $root, $handler)
    {
        $this->normalizers->add($class, $root, $handler);
    }

    public function addHydrator($class, $root, $handler)
    {
        $this->hydrators->add($class, $root, $handler);
    }

    public function serialize($var, $format)
    {
        return $this->getFormat($format)->serialize($var, $this->normalizers, new ParentNormalizerContext());
    }

    public function unserialize($var, $class, $format)
    {
        return $this->getFormat($format)->unserialize($var, $class, $this->hydrators);
    }

    private function getFormat($alias)
    {
        $format = $this->formats->get($alias);

        if(false === $format instanceof FormatInterface) {
            throw new \RuntimeException(sprintf('No registered format for alias %s!', $alias));
        }

        return $format;
    }
}
