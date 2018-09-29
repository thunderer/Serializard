<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\UnserializationFailureException;
use Thunder\Serializard\Hydrator\ReflectionHydrator;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorTest extends AbstractTestCase
{
    public function testReflectionHydratorInvalidClass()
    {
        $hydrator = new ReflectionHydrator('invalid', []);
        $this->expectExceptionClass(UnserializationFailureException::class);
        $hydrator([], new FallbackHydratorContainer());
    }
}
