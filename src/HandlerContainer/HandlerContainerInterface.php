<?php
namespace Thunder\Serializard\HandlerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface HandlerContainerInterface
{
    public function add($class, $alias, $handler);

    /**
     * @param string $class Class name
     *
     * @return string
     */
    public function getAlias($class);

    /**
     * @param string $class Class name
     *
     * @return callable
     */
    public function getHandler($class);
}
