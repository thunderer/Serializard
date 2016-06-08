<?php
namespace Thunder\Serializard\Tests\Fake\Inheritance;

class FakeClassParent extends FakeClassParentParent
{
    private $parentProperty;

    public function __construct($parent, $property)
    {
        parent::__construct($parent);

        $this->parentProperty = $property;
    }

    public function getParentProperty()
    {
        return $this->parentProperty;
    }
}
