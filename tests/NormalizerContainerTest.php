<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class NormalizerContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAlias()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add('stdClass', 'std', function() { return 'value'; });
        $normalizers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func_array($normalizers->getHandler('stdClass'), array()));
        $this->assertSame('value', call_user_func_array($normalizers->getHandler('DateTime'), array()));
    }

    public function testInterface()
    {
        $normalizers = new FallbackNormalizerContainer();
        $interfaceName = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $interfaceTypeA = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeA';
        $interfaceTypeB = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeB';
        $normalizers->add($interfaceName, 'type', function() { return 'type'; });

        $this->assertSame('type', call_user_func_array($normalizers->getHandler($interfaceTypeA), array()));
        $this->assertSame('type', call_user_func_array($normalizers->getHandler($interfaceTypeB), array()));
    }

    public function testInheritance()
    {
        $normalizers = new FallbackNormalizerContainer();
        $ancestorName = 'Thunder\Serializard\Tests\Fake\FakeUserParentParent';
        $parentName = 'Thunder\Serializard\Tests\Fake\FakeUserParent';
        $userName = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $normalizers->add($ancestorName, 'type', function() { return 'ancestor'; });

        $this->assertSame('ancestor', call_user_func_array($normalizers->getHandler($ancestorName), array()));
        $this->assertSame('ancestor', call_user_func_array($normalizers->getHandler($parentName), array()));
        $this->assertSame('ancestor', call_user_func_array($normalizers->getHandler($userName), array()));
    }

    public function testMultipleInterfacesException()
    {
        $typeInterface = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $typeAnother = 'Thunder\Serializard\Tests\Fake\Interfaces\AnotherTypeInterface';
        $typeMultiple = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeMultiple';

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($typeInterface, 'type', function() { return 'multiple'; });
        $normalizers->add($typeAnother, 'type', function() { return 'multiple'; });

        $this->setExpectedException('RuntimeException');
        $normalizers->getHandler($typeMultiple);
    }

    public function testInvalidClassOrInterfaceName()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->setExpectedException('RuntimeException');
        $normalizers->add('invalid', 'root', function() {});
    }

    public function testAliasForInvalidClass()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->setExpectedException('RuntimeException');
        $normalizers->addAlias('stdClass', 'DateTime');
    }

    public function testInvalidHandler()
    {
        $normalizers = new FallbackNormalizerContainer();
        $this->setExpectedException('RuntimeException');
        $normalizers->add('stdClass', 'name', 'invalid');
    }
}
