<?php
namespace Thunder\Serializard\Tests\Fake\Inheritance;

class FakeClassParentParent
{
    private $parentParentProperty;

    public function __construct($property)
    {
        $this->parentParentProperty = $property;
    }

    public function getParentParentProperty()
    {
        return $this->parentParentProperty;
    }
}
