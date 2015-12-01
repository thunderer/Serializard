<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface FormatInterface
{
    public function serialize($var, Handlers $handlers);

    public function unserialize($var, $class, Handlers $handlers);
}
