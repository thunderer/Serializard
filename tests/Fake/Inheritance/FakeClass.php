<?php
namespace Thunder\Serializard\Tests\Fake\Inheritance;

class FakeClass extends FakeClassParent
{
    private $property;

    public function __construct($parentParent, $parent, $property)
    {
        parent::__construct($parentParent, $parent);

        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
