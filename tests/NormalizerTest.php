<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Normalizer\GetObjectVarsNormalizer;
use Thunder\Serializard\Normalizer\ReflectionNormalizer;
use Thunder\Serializard\Tests\Fake\Inheritance\FakeClass;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;
use Thunder\Serializard\Tests\Fake\PropertyVisibility;

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

    public function testReflectionInheritance()
    {
        $normalizer = new ReflectionNormalizer();

        $this->assertSame(array(
            'property' => 'property',
            'parentProperty' => 'parent',
            'parentParentProperty' => 'parentParent',
        ), $normalizer(new FakeClass('parentParent', 'parent', 'property')));
    }

    public function testGetObjectVarsNormalizer()
    {
        $normalizer = new GetObjectVarsNormalizer();

        $this->assertSame(array(
            'public' => 'public',
        ), $normalizer(new PropertyVisibility()));
    }
}
