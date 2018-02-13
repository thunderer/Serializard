<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class NormalizerContainerTest extends AbstractTestCase
{
    public function testAlias()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add('stdClass', function() { return 'value'; });
        $normalizers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func($normalizers->getHandler('stdClass')));
        $this->assertSame('value', call_user_func($normalizers->getHandler('DateTime')));
    }

    public function testInterface()
    {
        $normalizers = new FallbackNormalizerContainer();
        $interfaceName = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $interfaceTypeA = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeA';
        $interfaceTypeB = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeB';
        $normalizers->add($interfaceName, function() { return 'type'; });

        $this->assertSame('type', call_user_func($normalizers->getHandler($interfaceTypeA)));
        $this->assertSame('type', call_user_func($normalizers->getHandler($interfaceTypeB)));
    }

    public function testInheritance()
    {
        $normalizers = new FallbackNormalizerContainer();
        $ancestorName = 'Thunder\Serializard\Tests\Fake\FakeUserParentParent';
        $parentName = 'Thunder\Serializard\Tests\Fake\FakeUserParent';
        $userName = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $normalizers->add($ancestorName, function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func($normalizers->getHandler($ancestorName)));
        $this->assertSame('ancestor', call_user_func($normalizers->getHandler($parentName)));
        $this->assertSame('ancestor', call_user_func($normalizers->getHandler($userName)));
    }

    public function testMultipleInterfacesException()
    {
        $typeInterface = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $typeAnother = 'Thunder\Serializard\Tests\Fake\Interfaces\AnotherTypeInterface';
        $typeMultiple = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeMultiple';

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($typeInterface, function() { return 'multiple'; });
        $normalizers->add($typeAnother, function() { return 'multiple'; });

        $this->expectExceptionClass(\RuntimeException::class);
        $normalizers->getHandler($typeMultiple);
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
