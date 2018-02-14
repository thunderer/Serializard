<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\InvalidFormatAliasException;
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
        $this->expectExceptionClass(InvalidFormatAliasException::class);
        $formats->add(new \stdClass(), new ArrayFormat());
    }
}
