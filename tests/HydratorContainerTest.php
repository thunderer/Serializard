<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\HydratorConflictException;
use Thunder\Serializard\Exception\HydratorNotFoundException;
use Thunder\Serializard\Exception\InvalidClassNameException;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\Tests\Fake\FakeUser;
use Thunder\Serializard\Tests\Fake\FakeUserParent;
use Thunder\Serializard\Tests\Fake\FakeUserParentParent;
use Thunder\Serializard\Tests\Fake\Interfaces\AnotherTypeInterface;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeA;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeB;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeMultiple;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class HydratorContainerTest extends AbstractTestCase
{
    public function testAlias()
    {
        $handlers = new FallbackHydratorContainer();
        $handlers->add(\stdClass::class, function() { return 'value'; });
        $handlers->addAlias(\DateTime::class, \stdClass::class);

        $this->assertSame('value', call_user_func($handlers->getHandler(\stdClass::class)));
        $this->assertSame('value', call_user_func($handlers->getHandler(\DateTime::class)));
    }

    public function testInterface()
    {
        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(TypeInterface::class, function() { return 'type'; });

        $this->assertSame('type', call_user_func($hydrators->getHandler(TypeA::class)));
        $this->assertSame('type', call_user_func($hydrators->getHandler(TypeB::class)));
    }

    public function testInheritance()
    {
        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(FakeUserParentParent::class, function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func($hydrators->getHandler(FakeUserParentParent::class)));
        $this->assertSame('ancestor', call_user_func($hydrators->getHandler(FakeUserParent::class)));
        $this->assertSame('ancestor', call_user_func($hydrators->getHandler(FakeUser::class)));
    }

    public function testMultipleInterfacesException()
    {
        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(TypeInterface::class, function() { return 'multiple'; });
        $hydrators->add(AnotherTypeInterface::class, function() { return 'multiple'; });

        $this->expectExceptionClass(HydratorConflictException::class);
        $hydrators->getHandler(TypeMultiple::class);
    }

    public function testInvalidClassOrInterfaceName()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectExceptionClass(InvalidClassNameException::class);
        $handlers->add('invalid', function() {});
    }

    public function testAliasForInvalidClass()
    {
        $handlers = new FallbackHydratorContainer();
        $this->expectExceptionClass(HydratorNotFoundException::class);
        $handlers->addAlias(\stdClass::class, \DateTime::class);
    }
}
