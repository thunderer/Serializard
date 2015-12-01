<?php
namespace Thunder\Serializard\FormatContainer;

use Thunder\Serializard\Format\FormatInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
interface FormatContainerInterface
{
    public function add($format, FormatInterface $handler);

    /**
     * @param $format
     *
     * @return FormatInterface
     */
    public function get($format);
}
