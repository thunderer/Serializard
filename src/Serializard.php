<?php
namespace Thunder\Serializard;

use Thunder\Serializard\Exception\FormatNotFoundException;
use Thunder\Serializard\Format\FormatInterface;
use Thunder\Serializard\FormatContainer\FormatContainerInterface as Formats;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;
use Thunder\Serializard\NormalizerContext\ParentNormalizerContext;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class Serializard
{
    private $normalizers;
    private $hydrators;
    /** @var Formats */
    private $formats;

    public function __construct(Formats $formats, Normalizers $normalizers, Hydrators $hydrators)
    {
        $this->normalizers = $normalizers;
        $this->hydrators = $hydrators;
        $this->formats = $formats;
    }

    public function serialize($var, $format, NormalizerContextInterface $context = null)
    {
        return $this->getFormat($format)->serialize($var, $this->normalizers, $this->getNormalizerContext($context, $var, $format));
    }

    public function unserialize($var, $class, $format)
    {
        return $this->getFormat($format)->unserialize($var, $class, $this->hydrators);
    }

    private function getNormalizerContext(NormalizerContextInterface $context = null, $var, $format)
    {
        $context = $context ?: new ParentNormalizerContext();

        return $context->withRoot($var)->withFormat($format);
    }

    private function getFormat($alias)
    {
        $format = $this->formats->get($alias);

        if(false === $format instanceof FormatInterface) {
            throw new FormatNotFoundException(sprintf('No registered format for alias %s.', $alias));
        }

        return $format;
    }
}
