<?php
namespace Thunder\Serializard\Tests;
use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HandlerContainer\HandlerContainer;
use Thunder\Serializard\Serializard;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeMultiple;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class HandlerContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testAlias()
    {
        $handlers = new HandlerContainer();
        $handlers->add('stdClass', 'std', function() {
            return 'value';
        });
        $handlers->addAlias('DateTime', 'stdClass');

        $this->assertSame('value', call_user_func_array($handlers->getHandler('stdClass'), array()));
        $this->assertSame('value', call_user_func_array($handlers->getHandler('DateTime'), array()));
    }

    public function testInvalidClassOrInterfaceName()
    {
        $this->setExpectedException('RuntimeException');
        $handlers = new HandlerContainer();
        $handlers->add('invalid', 'root', function() {});
    }

    public function testExceptionOnMultipleInterfaces()
    {
        $handlers = new HandlerContainer();
        $handlers->add('Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface', 'root', function() {});
        $handlers->add('Thunder\Serializard\Tests\Fake\Interfaces\AnotherTypeInterface', 'root', function() {});

        $normalizers = new HandlerContainer();

        $formats = new FormatContainer();
        $formats->add('array', new ArrayFormat());

        $serializard = new Serializard($formats, $handlers, $normalizers);
        $this->setExpectedException('RuntimeException');
        $serializard->serialize(new TypeMultiple(), 'array');
    }

    public function testAliasForInvalidClass()
    {
        $this->setExpectedException('RuntimeException');
        $handlers = new HandlerContainer();
        $handlers->addAlias('stdClass', 'DateTime');
    }

    public function testInvalidHandler()
    {
        $this->setExpectedException('RuntimeException');
        $handlers = new HandlerContainer();
        $handlers->add('stdClass', 'name', 'invalid');
    }
}
