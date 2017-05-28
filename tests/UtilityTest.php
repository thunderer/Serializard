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
        $this->expectException('InvalidArgumentException');
        new RootElementProviderUtility([0 => 'Class']);
    }

    public function testInvalidValue()
    {
        $this->expectException('InvalidArgumentException');
        new RootElementProviderUtility(['Class' => 0]);
    }

    public function testRootElementAliasNotFound()
    {
        $utility = new RootElementProviderUtility([]);
        $this->expectException('RuntimeException');
        $utility('invalid');
    }
}
