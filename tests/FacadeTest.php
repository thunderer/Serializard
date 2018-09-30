<?php
namespace Thunder\Serializard\Tests;

use Thunder\Serializard\Exception\FormatNotFoundException;
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\Normalizer\ReflectionNormalizer;
use Thunder\Serializard\SerializardFacade;
use Thunder\Serializard\Tests\Fake\FakeTag;
use Thunder\Serializard\Tests\Fake\FakeUser;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class FacadeTest extends AbstractTestCase
{
    public function testFacade()
    {
        $json = '{"id":1,"name":"em@ail.com"}';
        $user = new FakeUser(1, 'em@ail.com', new FakeTag(1, 'tag'));

        $facade = new SerializardFacade();
        $facade->addFormat('thunder', new JsonFormat());
        $facade->addNormalizer(FakeUser::class, new ReflectionNormalizer(['tag', 'tags']));
        $facade->addHydrator(FakeUser::class, function(array $data) {
            return new FakeUser($data['id'], $data['name'], new FakeTag(1, 'name'));
        });

        $this->assertSame($json, $facade->serialize($user, 'json'));
        $this->assertSame($json, $facade->serialize($user, 'thunder'));
        $this->assertInstanceOf(FakeUser::class, $facade->unserialize($json, FakeUser::class, 'json'));
        $this->assertSame(1, $facade->unserialize($json, FakeUser::class, 'json')->getId());
    }

    public function testExceptionInvalidFormat()
    {
        $facade = new SerializardFacade();
        $this->expectExceptionClass(FormatNotFoundException::class);
        $facade->serialize(new \stdClass(), 'invalid');
    }
}
