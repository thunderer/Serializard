<?php
namespace Thunder\Serializard\NormalizerContext;

interface NormalizerContextInterface
{
    /**
     * This method should create new context object with changed parent.
     *
     * @param object $root
     *
     * @return self
     */
    public function withRoot($root);

    /**
     * Returns root object passed to serialize() method.
     *
     * @return
     */
    public function getRoot();

    /**
     * This method should create new context object with changed parent.
     *
     * @param string $format
     *
     * @return self
     */
    public function withFormat($format);

    /**
     * Returns the format used to serialize given object.
     *
     * @return
     */
    public function getFormat();

    /**
     * This method should create new context object with changed parent.
     *
     * @param object $parent
     *
     * @return self
     */
    public function withParent($parent);

    /**
     * Returns parent of currently normalized object or null for root level.
     *
     * @return
     */
    public function getParent();
}
