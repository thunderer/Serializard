<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Utility\RootElementProviderUtility;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class UtilityTest extends AbstractTestCase
{
    public function testInvalidKey()
    {
        $this->expectExceptionClass(\RuntimeException::class);
        new RootElementProviderUtility([0 => 'Class']);
    }

    public function testInvalidValue()
    {
        $this->expectExceptionClass(\InvalidArgumentException::class);
        new RootElementProviderUtility(['Class' => 0]);
    }

    public function testRootElementAliasNotFound()
    {
        $utility = new RootElementProviderUtility([]);
        $this->expectExceptionClass(\RuntimeException::class);
        $utility('invalid');
    }
}
