<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\Exception\SerializationFailureException;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractFormat implements FormatInterface
{
    protected function doSerialize($var, Normalizers $handlers, NormalizerContextInterface $context, array $state = [], array $classes = [])
    {
        if(\is_object($var)) {
            $class = \get_class($var);
            // FIXME consider local handler cache to improve performance (solve parent class fallback issue)
            $handler = $handlers->getHandler($class);

            $hash = spl_object_hash($var);
            $classes[] = $class;
            if(isset($state[$hash])) {
                throw SerializationFailureException::fromCycle($classes);
            }
            $state[$hash] = 1;

            return $this->doSerialize($handler($var, $context), $handlers, $context->withParent($var), $state, $classes);
        }

        if(\is_array($var)) {
            $return = [];
            foreach($var as $key => $value) {
                $return[$key] = $this->doSerialize($value, $handlers, $context, $state, $classes);
            }

            return $return;
        }

        // FIXME: just return scalars, exception for different types like resources or streams

        return $var;
    }

    protected function doUnserialize($var, $class, Hydrators $hydrators)
    {
        return \call_user_func($hydrators->getHandler($class), $var, $hydrators);
    }
}
