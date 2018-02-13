<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Format\ArrayFormat;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\Format\XmlFormat;
use Thunder\Serializard\Format\YamlFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\Hydrator\ReflectionHydrator;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface;
use Thunder\Serializard\Normalizer\ReflectionNormalizer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;
use Thunder\Serializard\Serializard;
use Thunder\Serializard\Tests\Fake\Context\FakeNormalizerContext;
use Thunder\Serializard\Tests\Fake\FakeArticle;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;
use Thunder\Serializard\Tests\Fake\FakeUserParent;
use Thunder\Serializard\Tests\Fake\FakeUserParentParent;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeA;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeB;
use Thunder\Serializard\Tests\Fake\Interfaces\TypeInterface;
use Thunder\Serializard\Utility\RootElementProviderUtility;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class SerializardTest extends AbstractTestCase
{
    /** @dataProvider provideExamples */
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
        $this->assertSame(require $file.'.php', $array);

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
        $normalizers->add($interface, function(TypeInterface $type) {
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
        $normalizers->add($userClass, new ReflectionNormalizer());
        $normalizers->add($tagClass, new ReflectionNormalizer());

        $hydrators = new FallbackHydratorContainer();

        $formats = new FormatContainer();
        $formats->add('xml', new XmlFormat(new RootElementProviderUtility([
            FakeUser::class => 'user',
            FakeTag::class => 'tag',
        ])));
        $formats->add('yaml', new YamlFormat());
        $formats->add('json', new JsonFormat());
        $formats->add('array', new ArrayFormat());

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $this->expectExceptionClass(\RuntimeException::class);
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
        $normalizers->add($userClass, new ReflectionNormalizer());
        $normalizers->add($tagClass, function(FakeTag $tag) {
            return array(
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            );
        });

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add($userClass, function(array $data, Hydrators $handlers) use($tagClass) {
            $tagHandler = $handlers->getHandler($tagClass);

            $user = new FakeUser($data['id'], $data['name'], $tagHandler($data['tag'], $handlers));
            foreach($data['tags'] as $tag) {
                $user->addTag($tagHandler($tag, $handlers));
            }

            return $user;
        });
        $hydrators->add($tagClass, function(array $data, Hydrators $handlers) {
            return new FakeTag($data['id'], $data['name']);
        });

        $formats = new FormatContainer();
        $formats->add('xml', new XmlFormat(new RootElementProviderUtility([
            FakeUser::class => 'user',
            FakeTag::class => 'tag',
        ])));
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

        $normalizers->add($userClass.'ParentParent', function(FakeUserParentParent $user) { return 'ancestor'; });
        $this->assertSame('ancestor', $serializard->serialize($user, 'array'));

        $normalizers->add($userClass.'Parent', function(FakeUserParent $user) { return 'parent'; });
        $this->assertSame('parent', $serializard->serialize($user, 'array'));

        $normalizers->add($userClass, function(FakeUser $user) { return 'user'; });
        $this->assertSame('user', $serializard->serialize($user, 'array'));
    }

    public function testContext()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $articleClass = 'Thunder\Serializard\Tests\Fake\FakeArticle';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $formats = new FormatContainer();
        $formats->add('format', new ArrayFormat());

        $hydrators = new FallbackHydratorContainer();
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($articleClass, function(FakeArticle $article) {
            return $article->getTag();
        });
        $normalizers->add($userClass, function(FakeUser $user) {
            return $user->getTag();
        });
        $normalizers->add($tagClass, function(FakeTag $tag, NormalizerContextInterface $context) {
            return get_class($context->getParent());
        });

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'name');
        $user = new FakeUser(1, 'name', $tag);
        $article = new FakeArticle(1, 'title', $user, $tag);

        $this->assertSame($userClass, $serializard->serialize($user, 'format', new FakeNormalizerContext()));
        $context = new FakeNormalizerContext();
        $this->assertSame($articleClass, $serializard->serialize($article, 'format', $context));
        $this->assertSame(2, $context->getLevel());
    }

    public function testProcessWithCallbacks()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $articleClass = 'Thunder\Serializard\Tests\Fake\FakeArticle';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $formats = new FormatContainer();
        $formats->add('json', new JsonFormat());

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add($articleClass, function(array $data, FallbackHydratorContainer $hydrators) use($tagClass, $userClass) {
            $user = $hydrators->hydrate($userClass, $data['user']);
            $tag = $hydrators->hydrate($tagClass, $data['tag']);

            return new FakeArticle($data['id'], $data['title'], $user, $tag);
        });
        $hydrators->add($userClass, function(array $data, FallbackHydratorContainer $hydrators) use($tagClass) {
            $tag = $hydrators->hydrate($tagClass, $data['tag']);

            $user = new FakeUser($data['id'], $data['name'], $tag);
            foreach($data['tags'] as $tagData) {
                $user->addTag(call_user_func($hydrators->getHandler($tagClass), $tagData, $hydrators));
            }

            return $user;
        });
        $hydrators->add($tagClass, function(array $data, HydratorContainerInterface $hydrators) {
            return new FakeTag($data['id'], $data['name']);
        });

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($articleClass, function(FakeArticle $article, NormalizerContextInterface $context) {
            return array(
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'user' => $article->getUser(),
                'tag' => $article->getTag(),
            );
        });
        $normalizers->add($userClass, function(FakeUser $user, NormalizerContextInterface $context) {
            return array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'tag' => $user->getTag(),
                'tags' => $user->getTags(),
            );
        });
        $normalizers->add($tagClass, function(FakeTag $tag, NormalizerContextInterface $context) {
            return array(
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            );
        });

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'tag');
        $user = new FakeUser(1, 'user', $tag);
        $user->addTag(new FakeTag(1, 'tag'));
        $article = new FakeArticle(1, 'title', $user, $tag);

        $json = $serializard->serialize($article, 'json', new FakeNormalizerContext());

        $this->assertEquals($article, $serializard->unserialize($json, $articleClass, 'json'));
    }

    public function testProcessWithReflection()
    {
        $userClass = 'Thunder\Serializard\Tests\Fake\FakeUser';
        $articleClass = 'Thunder\Serializard\Tests\Fake\FakeArticle';
        $tagClass = 'Thunder\Serializard\Tests\Fake\FakeTag';

        $formats = new FormatContainer();
        $formats->add('json', new JsonFormat());

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add($articleClass, new ReflectionHydrator($articleClass, array(
            'user' => $userClass,
            'tag' => $tagClass,
        )));
        $hydrators->add($userClass, new ReflectionHydrator($userClass, array(
            'tag' => $tagClass,
            'tags' => $tagClass.'[]',
        )));
        $hydrators->add($tagClass, new ReflectionHydrator($tagClass, array()));

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add($articleClass, new ReflectionNormalizer());
        $normalizers->add($userClass, new ReflectionNormalizer());
        $normalizers->add($tagClass, new ReflectionNormalizer(array('user')));

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'tag');
        $user = new FakeUser(1, 'user', $tag);
        $user->addTag(new FakeTag(1, 'tag'));
        $tags = $user->getTags();
        $tags[0]->clearUser();
        $article = new FakeArticle(1, 'title', $user, $tag);

        $json = $serializard->serialize($article, 'json', new FakeNormalizerContext());

        $this->assertEquals($article, $serializard->unserialize($json, $articleClass, 'json'));
    }

    public function testInvalidSerializationFormat()
    {
        $this->expectExceptionClass(\RuntimeException::class);
        $this->getSerializard()->serialize(new \stdClass(), 'invalid');
    }

    public function testInvalidUnserializationFormat()
    {
        $this->expectExceptionClass(\RuntimeException::class);
        $this->getSerializard()->unserialize(new \stdClass(), \stdClass::class, 'invalid');
    }

    public function testMissingClassSerializationHandler()
    {
        $this->expectExceptionClass(\RuntimeException::class);
        $this->getSerializard()->serialize(new \stdClass(), 'json');
    }
}
