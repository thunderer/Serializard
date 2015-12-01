<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class JsonFormat extends AbstractFormat implements FormatInterface
{
    public function serialize($var, Handlers $handlers)
    {
        return json_encode($this->doSerialize($var, $handlers));
    }

    public function unserialize($var, $class, Handlers $handlers)
    {
        return call_user_func_array($handlers->getHandler($class), array(json_decode($var, true), $handlers));
    }
}
