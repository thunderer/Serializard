<?php
namespace Thunder\Serializard\NormalizerContext;

interface NormalizerContextInterface
{
    public function withParent($parent);
    public function getParent();
}
