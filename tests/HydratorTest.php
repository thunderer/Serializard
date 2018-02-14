<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\InvalidClassNameException;
use Thunder\Serializard\Hydrator\ReflectionHydrator;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorTest extends AbstractTestCase
{
    public function testReflectionHydratorInvalidClass()
    {
        $this->expectExceptionClass(InvalidClassNameException::class);
        new ReflectionHydrator('invalid', []);
    }
}
