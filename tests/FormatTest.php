<?php
namespace Thunder\Serializard\Tests;

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
        $this->expectExceptionClass(\RuntimeException::class);
        $format->unserialize(new \stdClass(), 'stdClass', new FallbackHydratorContainer());
    }

    public function testMissingUnserializationHandlerException()
    {
        $format = new ArrayFormat();
        $this->expectExceptionClass(\RuntimeException::class);
        $format->unserialize(array(), 'stdClass', new FallbackHydratorContainer());
    }

    public function testJsonEncodeSerializationFailureException()
    {
        $format = new JsonFormat();
        $this->expectExceptionClass(\RuntimeException::class); // Inf and NaN cannot be JSON encoded
        $format->serialize(INF, new FallbackNormalizerContainer(), new FakeNormalizerContext()); // INF is returned as zero on PHP <=5.4
    }
}
