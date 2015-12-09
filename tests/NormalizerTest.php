<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Normalizer\ReflectionNormalizer;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testReflectionSkip()
    {
        $normalizer = new ReflectionNormalizer(array('tag', 'tags'));
        $object = new FakeUser(12, 'XXX', new FakeTag(144, 'YYY'));

        $this->assertSame(array('id' => 12, 'name' => 'XXX'), $normalizer($object));
    }
}
