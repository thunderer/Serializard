<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\FormatNotFoundException;
use Thunder\Serializard\Exception\NormalizerNotFoundException;
use Thunder\Serializard\Exception\SerializationFailureException;
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

        $this->assertStringEqualsFile($file.'.json', $json."\n");
        $this->assertStringEqualsFile($file.'.yaml', $yaml);
        $this->assertStringEqualsFile($file.'.xml', $xml);
        $this->assertSame(require $file.'.php', $array);

        $this->assertSame($json, $serializard->serialize($serializard->unserialize($json, FakeUser::class, 'json'), 'json'));
        $this->assertSame($yaml, $serializard->serialize($serializard->unserialize($yaml, FakeUser::class, 'yaml'), 'yaml'));
        $this->assertSame($xml, $serializard->serialize($serializard->unserialize($xml, FakeUser::class, 'xml'), 'xml'));
        $this->assertSame($array, $serializard->serialize($serializard->unserialize($array, FakeUser::class, 'array'), 'array'));
    }

    public function provideExamples()
    {
        return [
            ['simple', function() {
                $user = new FakeUser(1, 'Thomas', new FakeTag(100, 'various'));
                $user->addTag(new FakeTag(10, 'sth'));
                $user->addTag(new FakeTag(11, 'xyz'));
                $user->addTag(new FakeTag(12, 'rnd'));

                return $user;
            }],
        ];
    }

    public function testInterfaces()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(TypeInterface::class, function(TypeInterface $type) {
            return [
                'type' => $type->getType(),
                'value' => $type->getValue(),
            ];
        });

        $hydrators = new FallbackHydratorContainer();

        $formats = new FormatContainer();
        $formats->add('array', new ArrayFormat());

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $this->assertSame(['type' => 'typeA', 'value' => 'valueA'], $serializard->serialize(new TypeA(), 'array'));
        $this->assertSame(['type' => 'typeB', 'value' => 'valueB'], $serializard->serialize(new TypeB(), 'array'));
    }

    /** @dataProvider provideCycles */
    public function testCycleException($var, $format)
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeUser::class, new ReflectionNormalizer());
        $normalizers->add(FakeTag::class, new ReflectionNormalizer());

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

        $this->expectExceptionClass(SerializationFailureException::class);
        $serializard->serialize($var, $format);
    }

    public function provideCycles()
    {
        $user = new FakeUser(1, 'Thomas', new FakeTag(100, 'various'));
        $user->addTag(new FakeTag(10, 'sth'));
        $user->addTag(new FakeTag(11, 'xyz'));
        $user->addTag(new FakeTag(12, 'rnd'));

        return [
            [$user, 'xml'],
            [$user, 'json'],
            [$user, 'yaml'],
            [$user, 'array'],
        ];
    }

    private function getSerializard()
    {
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeUser::class, new ReflectionNormalizer());
        $normalizers->add(FakeTag::class, function(FakeTag $tag) {
            return [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        });

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(FakeUser::class, function(array $data, Hydrators $handlers) {
            $tagHandler = $handlers->getHandler(FakeTag::class);

            $user = new FakeUser($data['id'], $data['name'], $tagHandler($data['tag'], $handlers));
            foreach($data['tags'] as $tag) {
                $user->addTag($tagHandler($tag, $handlers));
            }

            return $user;
        });
        $hydrators->add(FakeTag::class, function(array $data, Hydrators $handlers) {
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
        $user = new FakeUser(1, 'em@ail.com', new FakeTag(1, 'tag'));

        $formats = new FormatContainer();
        $formats->add('array', new ArrayFormat());
        $normalizers = new FallbackNormalizerContainer();
        $hydrators = new FallbackHydratorContainer();
        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $normalizers->add(FakeUserParentParent::class, function(FakeUserParentParent $user) { return 'ancestor'; });
        $this->assertSame('ancestor', $serializard->serialize($user, 'array'));

        $normalizers->add(FakeUserParent::class, function(FakeUserParent $user) { return 'parent'; });
        $this->assertSame('parent', $serializard->serialize($user, 'array'));

        $normalizers->add(FakeUser::class, function(FakeUser $user) { return 'user'; });
        $this->assertSame('user', $serializard->serialize($user, 'array'));
    }

    public function testContext()
    {
        $formats = new FormatContainer();
        $formats->add('format', new ArrayFormat());

        $hydrators = new FallbackHydratorContainer();
        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeArticle::class, function(FakeArticle $article) {
            return $article->getTag();
        });
        $normalizers->add(FakeUser::class, function(FakeUser $user) {
            return $user->getTag();
        });
        $normalizers->add(FakeTag::class, function(FakeTag $tag, NormalizerContextInterface $context) {
            return get_class($context->getParent());
        });

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'name');
        $user = new FakeUser(1, 'name', $tag);
        $article = new FakeArticle(1, 'title', $user, $tag);

        $this->assertSame(FakeUser::class, $serializard->serialize($user, 'format', new FakeNormalizerContext()));
        $context = new FakeNormalizerContext();
        $this->assertSame(FakeArticle::class, $serializard->serialize($article, 'format', $context));
        $this->assertSame(2, $context->getLevel());
    }

    public function testProcessWithCallbacks()
    {
        $formats = new FormatContainer();
        $formats->add('json', new JsonFormat());

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(FakeArticle::class, function(array $data, FallbackHydratorContainer $hydrators) {
            $user = $hydrators->hydrate(FakeUser::class, $data['user']);
            $tag = $hydrators->hydrate(FakeTag::class, $data['tag']);

            return new FakeArticle($data['id'], $data['title'], $user, $tag);
        });
        $hydrators->add(FakeUser::class, function(array $data, FallbackHydratorContainer $hydrators) {
            $tag = $hydrators->hydrate(FakeTag::class, $data['tag']);

            $user = new FakeUser($data['id'], $data['name'], $tag);
            foreach($data['tags'] as $tagData) {
                $user->addTag(call_user_func($hydrators->getHandler(FakeTag::class), $tagData, $hydrators));
            }

            return $user;
        });
        $hydrators->add(FakeTag::class, function(array $data, HydratorContainerInterface $hydrators) {
            return new FakeTag($data['id'], $data['name']);
        });

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeArticle::class, function(FakeArticle $article, NormalizerContextInterface $context) {
            return [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'user' => $article->getUser(),
                'tag' => $article->getTag(),
            ];
        });
        $normalizers->add(FakeUser::class, function(FakeUser $user, NormalizerContextInterface $context) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'tag' => $user->getTag(),
                'tags' => $user->getTags(),
            ];
        });
        $normalizers->add(FakeTag::class, function(FakeTag $tag, NormalizerContextInterface $context) {
            return [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        });

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'tag');
        $user = new FakeUser(1, 'user', $tag);
        $user->addTag(new FakeTag(1, 'tag'));
        $article = new FakeArticle(1, 'title', $user, $tag);

        $json = $serializard->serialize($article, 'json', new FakeNormalizerContext());

        $this->assertEquals($article, $serializard->unserialize($json, FakeArticle::class, 'json'));
    }

    public function testProcessWithReflection()
    {
        $formats = new FormatContainer();
        $formats->add('json', new JsonFormat());

        $hydrators = new FallbackHydratorContainer();
        $hydrators->add(FakeArticle::class, new ReflectionHydrator(FakeArticle::class, [
            'user' => FakeUser::class,
            'tag' => FakeTag::class,
        ]));
        $hydrators->add(FakeUser::class, new ReflectionHydrator(FakeUser::class, [
            'tag' => FakeTag::class,
            'tags' => FakeTag::class.'[]',
        ]));
        $hydrators->add(FakeTag::class, new ReflectionHydrator(FakeTag::class, []));

        $normalizers = new FallbackNormalizerContainer();
        $normalizers->add(FakeArticle::class, new ReflectionNormalizer());
        $normalizers->add(FakeUser::class, new ReflectionNormalizer());
        $normalizers->add(FakeTag::class, new ReflectionNormalizer(['user']));

        $serializard = new Serializard($formats, $normalizers, $hydrators);

        $tag = new FakeTag(1, 'tag');
        $user = new FakeUser(1, 'user', $tag);
        $user->addTag(new FakeTag(1, 'tag'));
        $tags = $user->getTags();
        $tags[0]->clearUser();
        $article = new FakeArticle(1, 'title', $user, $tag);

        $json = $serializard->serialize($article, 'json', new FakeNormalizerContext());

        $this->assertEquals($article, $serializard->unserialize($json, FakeArticle::class, 'json'));
    }

    public function testInvalidSerializationFormat()
    {
        $this->expectExceptionClass(FormatNotFoundException::class);
        $this->getSerializard()->serialize(new \stdClass(), 'invalid');
    }

    public function testInvalidUnserializationFormat()
    {
        $this->expectExceptionClass(FormatNotFoundException::class);
        $this->getSerializard()->unserialize(new \stdClass(), \stdClass::class, 'invalid');
    }

    public function testMissingClassSerializationHandler()
    {
        $this->expectExceptionClass(NormalizerNotFoundException::class);
        $this->getSerializard()->serialize(new \stdClass(), 'json');
    }
}
