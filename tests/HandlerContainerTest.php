<?php
namespace Thunder\Serializard\Tests;
use Thunder\Serializard\HandlerContainer\HandlerContainer;

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
