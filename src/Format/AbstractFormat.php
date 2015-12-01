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
            return $this->doSerialize(call_user_func_array($handlers->getHandler(get_class($var)), array($var)), $handlers);
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
