<?php
namespace Thunder\Serializard\Tests\Fake;

use Thunder\Clausure\Clausure;

final class FakeUser
{
    private $id;
    private $name;
    private $tags = array();
    private $tag;

    function __construct($id, $name, FakeTag $tag)
    {
        $this->id = $id;
        $this->name = $name;
        $this->tag = $tag;
    }

    public function setId($id) { $this->id = $id; return $this; }
    public function getId() { return $this->id; }

    public function setName($name) { $this->name = $name; return $this; }
    public function getName() { return $this->name; }

    public function getTag() { return $this->tag; }

    public function addTag(FakeTag $tag)
    {
        $this->tags[] = $tag;
    }

    public function removeTag(FakeTag $tag)
    {
        $this->tags = array_intersect($this->tags, Clausure::filterMethod($this->tags, 'getName', $tag->getName()));
    }

    public function getTags() { return $this->tags; }
}
