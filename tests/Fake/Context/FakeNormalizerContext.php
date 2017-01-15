<?php
namespace Thunder\Serializard\Tests\Fake\Context;

use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

final class FakeNormalizerContext implements NormalizerContextInterface
{
    private $root;
    private $format;
    private $parent;
    /** @var int */
    private $level = 0;

    public function __construct()
    {
    }

    public function withRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function withFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function withParent($parent)
    {
        $this->parent = $parent;
        $this->level++;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getLevel()
    {
        return $this->level;
    }
}
