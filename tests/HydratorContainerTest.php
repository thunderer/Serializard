<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\HydratorContainer\HydratorContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class HydratorContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAlias()
    {
        $handlers = new HydratorContainer();
        $handlers->add('stdClass', 'std', function() { return 'value'; });
        $handlers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func_array($handlers->getHandler('stdClass'), array()));
        $this->assertSame('value', call_user_func_array($handlers->getHandler('DateTime'), array()));
    }

    public function testInterface()
    {
        $hydrators = new HydratorContainer();
        $interfaceName = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $interfaceTypeA = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeA';
        $interfaceTypeB = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeB';
        $hydrators->add($interfaceName, 'type', function() { return 'type'; });

        $this->assertSame('type', call_user_func_array($hydrators->getHandler($interfaceTypeA), array()));
        $this->assertSame('type', call_user_func_array($hydrators->getHandler($interfaceTypeB), array()));
    }

    public function testInheritance()
    {
        $hydrators = new HydratorContainer();
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

        $hydrators = new HydratorContainer();
        $hydrators->add($typeInterface, 'type', function() { return 'multiple'; });
        $hydrators->add($typeAnother, 'type', function() { return 'multiple'; });

        $this->setExpectedException('RuntimeException');
        $hydrators->getHandler($typeMultiple);
    }

    public function testInvalidClassOrInterfaceName()
    {
        $handlers = new HydratorContainer();
        $this->setExpectedException('RuntimeException');
        $handlers->add('invalid', 'root', function() {});
    }

    public function testAliasForInvalidClass()
    {
        $handlers = new HydratorContainer();
        $this->setExpectedException('RuntimeException');
        $handlers->addAlias('stdClass', 'DateTime');
    }

    public function testInvalidHandler()
    {
        $handlers = new HydratorContainer();
        $this->setExpectedException('RuntimeException');
        $handlers->add('stdClass', 'name', 'invalid');
    }
}
