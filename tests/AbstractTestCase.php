<?php
namespace Thunder\Serializard\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
abstract class AbstractTestCase extends TestCase
{
    public function expectExceptionClass($exception)
    {
        version_compare(phpversion(), '7.0.0') > 0
            ? parent::expectException($exception)
            : $this->setExpectedException($exception);
    }
}
