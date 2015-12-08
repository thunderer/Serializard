<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\Format\XmlFormat;
use Thunder\Serializard\Format\YamlFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HandlerContainer\HandlerContainer;
use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;
use Thunder\Serializard\Serializard;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class SerializardTest extends \PHPUnit_Framework_TestCase
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

    private function getSerializard()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $normalizers = new HandlerContainer();
        $normalizers->add($userClass, 'user', function(FakeUser $user) {
            return array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'tag' => $user->getTag(),
                'tags' => $user->getTags(),
            );
        });
        $normalizers->add($tagClass, 'tag', function(FakeTag $tag) {
            return array(
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            );
        });

        $hydrators = new HandlerContainer();
        $hydrators->add($userClass, 'user', function(array $data, Handlers $handlers) use($tagClass) {
            $tagHandler = $handlers->getHandler($tagClass);

            $user = new FakeUser($data['id'], $data['name'], $tagHandler($data['tag'], $handlers));
            foreach($data['tags'] as $tag) {
                $user->addTag($tagHandler($tag, $handlers));
            }

            return $user;
        });
        $hydrators->add($tagClass, 'tag', function(array $data, Handlers $handlers) {
            return new FakeTag($data['id'], $data['name']);
        });

        $formats = new FormatContainer();
        $formats->add('xml', new XmlFormat());
        $formats->add('yaml', new YamlFormat());
        $formats->add('json', new JsonFormat());
        $formats->add('array', new ArrayFormat());

        return new Serializard($formats, $normalizers, $hydrators);
    }

    public function testInvalidSerializationFormat()
    {
        $this->setExpectedException('RuntimeException');
        $this->getSerializard()->serialize(new \stdClass(), 'invalid');
    }

    public function testInvalidUnserializationFormat()
    {
        $this->setExpectedException('RuntimeException');
        $this->getSerializard()->unserialize(new \stdClass(), 'stdClass', 'invalid');
    }

    public function testMissingClassSerializationHandler()
    {
        $this->setExpectedException('RuntimeException');
        $this->getSerializard()->serialize(new \stdClass(), 'json');
    }
}
