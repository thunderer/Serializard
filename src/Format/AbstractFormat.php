<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractFormat implements FormatInterface
{
    protected function doSerialize($var, Handlers $handlers, array $state = array(), array $classes = array())
    {
        if(is_object($var)) {
            $class = get_class($var);
            $handler = $handlers->getHandler($class);

            if(null === $handler) {
                throw new \RuntimeException(sprintf('No serialization handler for class %s!', $class));
            }

            $newState = array_merge($state, array(spl_object_hash($var)));
            $newClasses = array_merge($classes, array(get_class($var)));
            if(count(array_keys($state, spl_object_hash($var), true)) > 1) {
                throw new \RuntimeException('Nesting cycle: '.implode(' -> ', $newClasses));
            }

            return $this->doSerialize(call_user_func_array($handler, array($var)), $handlers, $newState, $newClasses);
        }

        if(is_array($var)) {
            $return = array();
            foreach($var as $key => $value) {
                $return[$key] = $this->doSerialize($value, $handlers, $state, $classes);
            }

            return $return;
        }

        return $var;
    }
}
