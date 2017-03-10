<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayUnserializeInvalidTypeException()
    {
        $format = new ArrayFormat();
        $this->setExpectedException('RuntimeException');
        $format->unserialize(new \stdClass(), 'stdClass', new FallbackHydratorContainer());
    }

    public function testMissingUnserializationHandlerException()
    {
        $format = new ArrayFormat();
        $this->setExpectedException('RuntimeException');
        $format->unserialize(array(), 'stdClass', new FallbackHydratorContainer());
    }

    public function testJsonEncodeSerializationFailureException()
    {
        $format = new JsonFormat();
        $this->setExpectedException('RuntimeException'); // Inf and NaN cannot be JSON encoded
        $this->assertSame(INF, $format->serialize(INF, new FallbackNormalizerContainer()));
    }
}
