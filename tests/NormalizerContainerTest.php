<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
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
final class NormalizerContainerTest extends AbstractTestCase
{
    public function testAlias()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(\stdClass::class, function() { return 'value'; });
        $normalizers->addAlias(\DateTime::class, \stdClass::class);

        $this->assertSame('value', call_user_func($normalizers->getHandler(\stdClass::class)));
        $this->assertSame('value', call_user_func($normalizers->getHandler(\DateTime::class)));
    }

    public function testInterface()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(TypeInterface::class, function() { return 'type'; });

        $this->assertSame('type', call_user_func($normalizers->getHandler(TypeA::class)));
        $this->assertSame('type', call_user_func($normalizers->getHandler(TypeB::class)));
    }

    public function testInheritance()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeUserParentParent::class, function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func($normalizers->getHandler(FakeUserParentParent::class)));
        $this->assertSame('ancestor', call_user_func($normalizers->getHandler(FakeUserParent::class)));
        $this->assertSame('ancestor', call_user_func($normalizers->getHandler(FakeUser::class)));
    }

    public function testMultipleInterfacesException()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(TypeInterface::class, function() { return 'multiple'; });
        $normalizers->add(AnotherTypeInterface::class, function() { return 'multiple'; });

        $this->expectExceptionClass(\RuntimeException::class);
        $normalizers->getHandler(TypeMultiple::class);
    }

    public function testInvalidClassOrInterfaceName()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->expectExceptionClass(\RuntimeException::class);
        $normalizers->add('invalid', function() {});
    }

    public function testAliasForInvalidClass()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->expectExceptionClass(\RuntimeException::class);
        $normalizers->addAlias(\stdClass::class, \DateTime::class);
    }

    public function testInvalidHandler()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->expectExceptionClass(\RuntimeException::class);
        $normalizers->add(\stdClass::class, 'invalid');
    }
}
