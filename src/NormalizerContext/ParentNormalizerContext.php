<?php
namespace Thunder\Serializard\NormalizerContext;

final class ParentNormalizerContext implements NormalizerContextInterface
{
    private $parent;

    public function __construct()
    {
    }

    public function withParent($parent)
    {
        $context = new self();
        $context->parent = $parent;

        return $context;
    }

    public function getParent()
    {
        return $this->parent;
    }
}
