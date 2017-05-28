<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\Format\XmlFormat;
use Thunder\Serializard\Format\YamlFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\Normalizer\ReflectionNormalizer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\Serializard;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;
use Thunder\Serializard\Tests\Fake\FakeUserParent;
use Thunder\Serializard\Tests\Fake\FakeUserParentParent;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeA;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeB;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class SerializardTest extends AbstractTestCase
{
    /**
     * @param string $prefix
     * @param callable $factory
     *
     * @dataProvider provideExamples
     */
    public function testSerializard($prefix, $factory)
    {
        $object = $factory();

        $serializard = $this->getSerializard();

        $file = __DIR__.'/examples/'.$prefix;

        $json = $serializard->serialize($object, 'json');
        $yaml = $serializard->serialize($object, 'yaml');
        $xml = $serializard->serialize($object, 'xml');
        $array = $serializard->serialize($object, 'array');

        $this->assertSame(file_get_contents($file.'.json'), $json."\n");
        $this->assertSame(file_get_contents($file.'.yaml'), $yaml);
        $this->assertSame(file_get_contents($file.'.xml'), $xml);
        $this->assertSame(require($file.'.php'), $array);

        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';

        $this->assertSame($json, $serializard->serialize($serializard->unserialize($json, $userClass, 'json'), 'json'));
        $this->assertSame($yaml, $serializard->serialize($serializard->unserialize($yaml, $userClass, 'yaml'), 'yaml'));
        $this->assertSame($xml, $serializard->serialize($serializard->unserialize($xml, $userClass, 'xml'), 'xml'));
        $this->assertSame($array, $serializard->serialize($serializard->unserialize($array, $userClass, 'array'), 'array'));
    }

    public function provideExamples()
    {
        return array(
            array('simple', function() {
                $user = new FakeUser(1, 'Thomas', new FakeTag(100, 'various'));
                $user->addTag(new FakeTag(10, 'sth'));
                $user->addTag(new FakeTag(11, 'xyz'));
                $user->addTag(new FakeTag(12, 'rnd'));

                return $user;
            }),
        );
    }

    public function testInterfaces()
    {
        $interface = 'Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface';
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($interface, 'type', function(TypeInterface $type) {
            return array(
                'type' => $type->getType(),
                'value' => $type->getValue(),
            );
        });

        $hydrators = new FallbackHydratorContainer();

        $formats = new FormatContainer();
        $formats->add('array', new ArrayFormat());

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $this->assertSame(array('type' => 'typeA', 'value' => 'valueA'), $serializard->serialize(new TypeA(), 'array'));
        $this->assertSame(array('type' => 'typeB', 'value' => 'valueB'), $serializard->serialize(new TypeB(), 'array'));
    }

    /** @dataProvider provideCycles */
    public function testCycleException($var, $format)
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($userClass, 'user', new ReflectionNormalizer());
        $normalizers->add($tagClass, 'tag', new ReflectionNormalizer());

        $hydrators = new FallbackHydratorContainer();

        $formats = new FormatContainer();
        $formats->add('xml', new XmlFormat());
        $formats->add('yaml', new YamlFormat());
        $formats->add('json', new JsonFormat());
        $formats->add('array', new ArrayFormat());

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $this->expectException('RuntimeException');
        $serializard->serialize($var, $format);
    }

    public function provideCycles()
    {
        $user = new FakeUser(1, 'Thomas', new FakeTag(100, 'various'));
        $user->addTag(new FakeTag(10, 'sth'));
        $user->addTag(new FakeTag(11, 'xyz'));
        $user->addTag(new FakeTag(12, 'rnd'));

        return array(
            array($user, 'xml'),
            array($user, 'json'),
            array($user, 'yaml'),
            array($user, 'array'),
        );
    }

    private function getSerializard()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($userClass, 'user', new ReflectionNormalizer());
        $normalizers->add($tagClass, 'tag', function(FakeTag $tag) {
            return array(
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            );
        });

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add($userClass, 'user', function(array $data, Hydrators $handlers) use($tagClass) {
            $tagHandler = $handlers->getHandler($tagClass);

            $user = new FakeUser($data['id'], $data['name'], $tagHandler($data['tag'], $handlers));
            foreach($data['tags'] as $tag) {
                $user->addTag($tagHandler($tag, $handlers));
            }

            return $user;
        });
        $hydrators->add($tagClass, 'tag', function(array $data, Hydrators $handlers) {
            return new FakeTag($data['id'], $data['name']);
        });

        $formats = new FormatContainer();
        $formats->add('xml', new XmlFormat());
        $formats->add('yaml', new YamlFormat());
        $formats->add('json', new JsonFormat());
        $formats->add('array', new ArrayFormat());

        return new Serializard($formats, $normalizers, $hydrators);
    }

    public function testParent()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $user = new FakeUser(1, 'em@ail.com', new FakeTag(1, 'tag'));

        $formats = new FormatContainer();
        $formats->add('array', new ArrayFormat());
        $normalizers = new FallbackNormalizerContainer();
        $hydrators = new FallbackHydratorContainer();
        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $normalizers->add($userClass.'ParentParent', 'user', function(FakeUserParentParent $user) { return 'ancestor'; });
        $this->assertSame('ancestor', $serializard->serialize($user, 'array'));

        $normalizers->add($userClass.'Parent', 'user', function(FakeUserParent $user) { return 'parent'; });
        $this->assertSame('parent', $serializard->serialize($user, 'array'));

        $normalizers->add($userClass, 'user', function(FakeUser $user) { return 'user'; });
        $this->assertSame('user', $serializard->serialize($user, 'array'));
    }

    public function testInvalidSerializationFormat()
    {
        $this->expectException('RuntimeException');
        $this->getSerializard()->serialize(new \stdClass(), 'invalid');
    }

    public function testInvalidUnserializationFormat()
    {
        $this->expectException('RuntimeException');
        $this->getSerializard()->unserialize(new \stdClass(), 'stdClass', 'invalid');
    }

    public function testMissingClassSerializationHandler()
    {
        $this->expectException('RuntimeException');
        $this->getSerializard()->serialize(new \stdClass(), 'json');
    }
}
