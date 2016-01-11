<?php
namespace Thunder\Serializard\HandlerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface HandlerContainerInterface
{
    /**
     * @param string $class Class name
     *
     * @return string
     */
    public function getRoot($class);

    /**
     * @param string $class Class name
     *
     * @return callable
     */
    public function getHandler($class);
}
