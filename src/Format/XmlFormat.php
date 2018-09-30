<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\Exception\SerializationFailureException;
use Thunder\Serializard\NormalizerContainer\NormalizerContainerInterface as Normalizers;
use Thunder\Serializard\HydratorContainer\HydratorContainerInterface as Hydrators;
use Thunder\Serializard\NormalizerContext\NormalizerContextInterface;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class XmlFormat implements FormatInterface
{
    /** @var callable */
    private $rootProvider;

    public function __construct(callable $rootProvider)
    {
        $this->rootProvider = $rootProvider;
    }

    public function serialize($var, Normalizers $normalizers, NormalizerContextInterface $context)
    {
        return $this->doSerialize($var, $normalizers, $context);
    }

    private function doSerialize($var, Normalizers $normalizers, NormalizerContextInterface $context, \DOMNode $doc = null, $parent = null, $key = null, array $state = [], array $classes = [])
    {
        $isRoot = ($doc === null);
        $doc = $doc ?: new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $parent = $parent ?: $doc;

        $this->serializeValue($var, $normalizers, $context, $doc, $parent, $key, $state, $classes);

        return $isRoot ? $doc->saveXML() : null;
    }

    private function serializeValue($var, Normalizers $normalizers, NormalizerContextInterface $context, \DOMNode $doc, $parent, $key, array $state = [], array $classes = [])
    {
        /** @var \DOMDocument|\DOMElement $doc */
        /** @var \DOMDocument|\DOMElement $parent */
        if(\is_object($var)) {
            $class = \get_class($var);
            $handler = $normalizers->getHandler($class);
            $arr = $handler($var);

            $hash = spl_object_hash($var);
            $classes[] = $class;
            if(isset($state[$hash])) {
                throw SerializationFailureException::fromCycle($classes);
            }
            $state[$hash] = 1;

            $this->doSerialize($arr, $normalizers, $context->withParent($var), $doc, $parent, $this->getRoot($class), $state, $classes);

            return;
        }

        if(\is_array($var)) {
            $item = $key ? $doc->createElement($key) : $parent;
            foreach($var as $index => $value) {
                $this->doSerialize($value, $normalizers, $context, $doc, $item, $index, $state, $classes);
            }
            !$key ?: $parent->appendChild($item);

            return;
        }

        $parent->appendChild($doc->createElement($key, $var));
    }

    public function unserialize($var, $class, Hydrators $hydrators)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($var);
        $data = $this->parse($doc, $doc);
        $hydrator = $hydrators->getHandler($class);

        return $hydrator($data[$this->getRoot($class)], $hydrators);
    }

    private function parse(\DOMDocument $doc, $parent = null)
    {
        $ret = [];
        /** @var \DOMElement $parent */
        /** @var \DOMElement $node */
        $tags = [];
        foreach($parent->childNodes as $node) {
            if($node->nodeName === '#text') {
                continue;
            }
            if($node->childNodes->length === 1 && $node->childNodes->item(0) instanceof \DOMText) {
                $ret[$node->tagName] = $node->childNodes->item(0)->nodeValue;
                continue;
            }
            $result = $this->parse($doc, $node);
            if(array_key_exists($node->tagName, $ret)) {
                $tags[] = $node->tagName;
                $ret = [$ret[$node->tagName]];
                $ret[] = $result;
                continue;
            }
            if(\in_array($node->tagName, $tags, true)) {
                $ret[] = $result;
                continue;
            }
            $ret[$node->tagName] = $result;
        }

        return $ret;
    }

    private function getRoot($class)
    {
        return \call_user_func($this->rootProvider, $class);
    }
}
