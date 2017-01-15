<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Hydrator\ReflectionHydrator;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorTest extends AbstractTestCase
{
    public function testReflectionHydratorInvalidClass()
    {
        $this->expectException('InvalidArgumentException');
        new ReflectionHydrator('invalid', []);
    }
}
