<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractFormat implements FormatInterface
{
    protected function doSerialize($var, Handlers $handlers)
    {
        if(is_object($var)) {
            $class = get_class($var);
            $handler = $handlers->getHandler($class);

            if(null === $handler) {
                throw new \RuntimeException(sprintf('No serialization handler for class %s!', $class));
            }

            return $this->doSerialize(call_user_func_array($handler, array($var)), $handlers);
        }

        if(is_array($var)) {
            $return = array();
            foreach($var as $key => $value) {
                $return[$key] = $this->doSerialize($value, $handlers);
            }

            return $return;
        }

        return $var;
    }
}
