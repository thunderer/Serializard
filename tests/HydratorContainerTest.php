<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorContainerTest extends AbstractTestCase
{
    public function testAlias()
    {
        $handlers = new FallbackHydratorContainer();
        $handlers->add('stdClass', function() { return 'value'; });
        $handlers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func($handlers->getHandler('stdClass')));
        $this->assertSame('value', call_user_func($handlers->getHandler('DateTime')));
    }

    public function testInterface()
    {
        $hydrators = new FallbackHydratorContainer();
        $interfaceName = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $interfaceTypeA = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeA';
        $interfaceTypeB = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeB';
        $hydrators->add($interfaceName, function() { return 'type'; });

        $this->assertSame('type', call_user_func($hydrators->getHandler($interfaceTypeA)));
        $this->assertSame('type', call_user_func($hydrators->getHandler($interfaceTypeB)));
    }

    public function testInheritance()
    {
        $hydrators = new FallbackHydratorContainer();
        $ancestorName = 'Thunder\Serializard\Tests\Fake\FakeUserParentParent';
        $parentName = 'Thunder\Serializard\Tests\Fake\FakeUserParent';
        $userName = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $hydrators->add($ancestorName, function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func($hydrators->getHandler($ancestorName)));
        $this->assertSame('ancestor', call_user_func($hydrators->getHandler($parentName)));
        $this->assertSame('ancestor', call_user_func($hydrators->getHandler($userName)));
    }

    public function testInvalidClassOrInterfaceName()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectException('RuntimeException');
        $handlers->add('invalid', function() {});
    }

    public function testAliasForInvalidClass()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectException('RuntimeException');
        $handlers->addAlias('stdClass', 'DateTime');
    }

    public function testInvalidHandler()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectException('RuntimeException');
        $handlers->add('stdClass', 'invalid');
    }
}
