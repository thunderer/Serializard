<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\HydratorContainer\HydratorContainer;
use Thunder\Serializard\NormalizerContainer\NormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayUnserializeInvalidTypeException()
    {
        $format = new ArrayFormat();
        $this->setExpectedException('RuntimeException');
        $format->unserialize(new \stdClass(), 'stdClass', new HydratorContainer());
    }

    public function testMissingUnserializationHandlerException()
    {
        $format = new ArrayFormat();
        $this->setExpectedException('RuntimeException');
        $format->unserialize(array(), 'stdClass', new HydratorContainer());
    }
}
