<?php
namespace Thunder\Serializard\NormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface NormalizerContainerInterface
{
    /**
     * @param string $class Class name
     *
     * @return callable
     */
    public function getHandler($class);
}
