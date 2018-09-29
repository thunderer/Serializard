<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FormatContainerTest extends AbstractTestCase
{
    public function testExceptionOnInvalidFormatAlias()
    {
        $formats = new FormatContainer();
        $this->expectExceptionClass(\InvalidArgumentException::class);
        $formats->add(new \stdClass(), new ArrayFormat());
    }
}
