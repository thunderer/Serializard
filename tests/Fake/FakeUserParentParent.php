<?php
namespace Thunder\Serializard\Tests\Fake;

class FakeUserParentParent
{
    protected $id;
    protected $name;
    protected $tag;
    /** @var FakeTag[] */
    protected $tags = [];

    public function __construct($id, $name, FakeTag $tag)
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
        $tag->setUser($this);

        $this->tags[] = $tag;
    }

    public function removeTag(FakeTag $removed)
    {
        foreach($this->tags as $key => $tag) {
            if($removed->getName() === $tag->getName()) {
                unset($this->tags[$key]);
            }
        }
    }

    public function getTags() { return $this->tags; }
}
