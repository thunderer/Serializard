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
        $handlers->add('stdClass', 'std', function() { return 'value'; });
        $handlers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func_array($handlers->getHandler('stdClass'), array()));
        $this->assertSame('value', call_user_func_array($handlers->getHandler('DateTime'), array()));
    }

    public function testInterface()
    {
        $hydrators = new FallbackHydratorContainer();
        $interfaceName = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $interfaceTypeA = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeA';
        $interfaceTypeB = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeB';
        $hydrators->add($interfaceName, 'type', function() { return 'type'; });

        $this->assertSame('type', call_user_func_array($hydrators->getHandler($interfaceTypeA), array()));
        $this->assertSame('type', call_user_func_array($hydrators->getHandler($interfaceTypeB), array()));
    }

    public function testInheritance()
    {
        $hydrators = new FallbackHydratorContainer();
        $ancestorName = 'Thunder\Serializard\Tests\Fake\FakeUserParentParent';
        $parentName = 'Thunder\Serializard\Tests\Fake\FakeUserParent';
        $userName = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $hydrators->add($ancestorName, 'type', function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func_array($hydrators->getHandler($ancestorName), array()));
        $this->assertSame('ancestor', call_user_func_array($hydrators->getHandler($parentName), array()));
        $this->assertSame('ancestor', call_user_func_array($hydrators->getHandler($userName), array()));
    }

    public function testMultipleInterfacesException()
    {
        $typeInterface = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $typeAnother = 'Thunder\Serializard\Tests\Fake\Interfaces\AnotherTypeInterface';
        $typeMultiple = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeMultiple';

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add($typeInterface, 'type', function() { return 'multiple'; });
        $hydrators->add($typeAnother, 'type', function() { return 'multiple'; });

        $this->expectException('RuntimeException');
        $hydrators->getHandler($typeMultiple);
    }

    public function testInvalidClassOrInterfaceName()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectException('RuntimeException');
        $handlers->add('invalid', 'root', function() {});
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
        $handlers->add('stdClass', 'name', 'invalid');
    }
}
