<?php
namespace Thunder\Serializard\Tests\Fake;

final class FakeTag
{
    private $id;
    private $name;

    function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setId($id) { $this->id = $id; return $this; }
    public function getId() { return $this->id; }

    public function setName($name) { $this->name = $name; return $this; }
    public function getName() { return $this->name; }
}
