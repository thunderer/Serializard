<?php
namespace Thunder\Serializard\Format;

use Symfony\Component\Yaml\Yaml;
use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class YamlFormat extends AbstractFormat implements FormatInterface
{
    public function serialize($var, Handlers $handlers)
    {
        return Yaml::dump($this->doSerialize($var, $handlers), 2, 2);
    }

    public function unserialize($var, $class, Handlers $handlers)
    {
        return call_user_func_array($handlers->getHandler($class), array(Yaml::parse($var), $handlers));
    }
}
