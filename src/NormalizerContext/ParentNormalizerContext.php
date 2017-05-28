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
        $context = new self();

        $context->root = $root;
        $context->format = $this->format;
        $context->parent = $this->parent;
        $context->level = $this->level;

        return $context;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function withFormat($format)
    {
        $context = new self();

        $context->root = $this->root;
        $context->format = $format;
        $context->parent = $this->parent;
        $context->level = $this->level;

        return $context;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function withParent($parent)
    {
        $context = new self();

        $context->root = $this->root;
        $context->format = $this->format;
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
