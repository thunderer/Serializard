<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\HydratorNotFoundException;
use Thunder\Serializard\Exception\SerializationFailureException;
use Thunder\Serializard\Exception\UnserializationFailureException;
use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\Tests\Fake\Context\FakeNormalizerContext;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatTest extends AbstractTestCase
{
    public function testArrayUnserializeInvalidTypeException()
    {
        $format = new ArrayFormat();
        $this->expectExceptionClass(UnserializationFailureException::class);
        $format->unserialize(new \stdClass(), \stdClass::class, new FallbackHydratorContainer());
    }

    public function testMissingUnserializationHandlerException()
    {
        $format = new ArrayFormat();
        $this->expectExceptionClass(HydratorNotFoundException::class);
        $format->unserialize([], \stdClass::class, new FallbackHydratorContainer());
    }

    public function testJsonEncodeSerializationFailureException()
    {
        $format = new JsonFormat();
        $this->expectExceptionClass(SerializationFailureException::class); // Inf and NaN cannot be JSON encoded
        $format->serialize(INF, new FallbackNormalizerContainer(), new FakeNormalizerContext()); // INF is returned as zero on PHP <=5.4
    }
}
