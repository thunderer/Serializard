<?php
namespace Thunder\Serializard\Format;

use Thunder\Serializard\HandlerContainer\HandlerContainerInterface as Handlers;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class XmlFormat implements FormatInterface
{
    public function serialize($var, Handlers $handlers)
    {
        return $this->doSerialize($var, $handlers);
    }

    private function doSerialize($var, Handlers $handlers, $doc = null, $parent = null, $key = null, array $state = array(), array $classes = array())
    {
        $isRoot = ($doc === null);
        $doc = $doc ?: new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $parent = $parent ?: $doc;

        $this->serializeValue($var, $handlers, $doc, $parent, $key, $state, $classes);

        return $isRoot ? $doc->saveXML() : null;
    }

    private function serializeValue($var, Handlers $handlers, $doc, $parent, $key, array $state = array(), array $classes = array())
    {
        /** @var \DOMDocument|\DOMElement $doc */
        /** @var \DOMDocument|\DOMElement $parent */
        if(is_object($var)) {
            $handler = $handlers->getHandler(get_class($var));
            $arr = $handler($var);

            $hash = spl_object_hash($var);
            $classes[] = get_class($var);
            if(isset($state[$hash])) {
                throw new \RuntimeException('Nesting cycle: '.implode(' -> ', $classes));
            }
            $state[$hash] = 1;

            $this->doSerialize($arr, $handlers, $doc, $parent, $handlers->getRoot(get_class($var)), $state, $classes);

            return;
        }

        if(is_array($var)) {
            $item = $key ? $doc->createElement($key) : $parent;
            foreach($var as $index => $value) {
                $this->doSerialize($value, $handlers, $doc, $item, $index, $state, $classes);
            }
            !$key ?: $parent->appendChild($item);

            return;
        }

        $parent->appendChild($doc->createElement($key, $var));
    }

    public function unserialize($var, $class, Handlers $handlers)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($var);
        $data = $this->parse($doc, $doc);
        $hydrator = $handlers->getHandler($class);

        return $hydrator($data[$handlers->getRoot($class)], $handlers);
    }

    private function parse(\DOMDocument $doc, $parent = null)
    {
        $ret = array();
        /** @var \DOMElement $parent */
        /** @var \DOMElement $node */
        $tags = array();
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
                $ret = array($ret[$node->tagName]);
                $ret[] = $result;
                continue;
            }
            if(in_array($node->tagName, $tags)) {
                $ret[] = $result;
                continue;
            }
            $ret[$node->tagName] = $result;
        }

        return $ret;
    }
}
