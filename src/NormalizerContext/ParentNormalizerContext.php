<?php
namespace Thunder\Serializard\NormalizerContext;

final class ParentNormalizerContext implements NormalizerContextInterface
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
        $context = clone $this;

        $context->root = $root;

        return $context;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function withFormat($format)
    {
        $context = clone $this;

        $context->format = $format;

        return $context;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function withParent($parent)
    {
        $context = clone $this;

        $context->parent = $parent;
        $context->level = $this->level + 1;

        return $context;
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
