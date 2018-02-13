<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\NormalizerContext\ParentNormalizerContext;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class NormalizerContextTest extends AbstractTestCase
{
    public function testParentNormalizerContext()
    {
        $ctx = new ParentNormalizerContext();
        $this->assertSame(0, $ctx->getLevel());
        $this->assertNull($ctx->getParent());
        $this->assertNull($ctx->getFormat());
        $this->assertNull($ctx->getRoot());

        $object = new \stdClass();
        /** @var ParentNormalizerContext $ctx */
        $ctx = $ctx->withRoot($object)->withFormat('json');

        $ctx = $ctx->withParent($object);
        $this->assertSame(1, $ctx->getLevel());
        $this->assertSame($object, $ctx->getParent());
        $this->assertSame($object, $ctx->getRoot());
        $this->assertSame('json', $ctx->getFormat());
    }
}
