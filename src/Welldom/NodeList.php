<?php

/*
 * This file is part of the Welldom package.
 *
 * (c) Groupe Express Roularta
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Welldom;

/**
 * DOM Node List extension. Batch actions on its items.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 *
 * @method array getNodePath() getNodePath()
 * @method array getLineNo() getLineNo()
 * @method array isSameNode() isSameNode(\DOMNode $node)
 * @method array cloneNode() cloneNode(bool $deep)
 * @method array hasAttributes() hasAttributes()
 * @method array hasChildNodes() hasChildNodes()
 */
class NodeList implements NodeInterface, \IteratorAggregate
{
    /**
     * @var \DOMNodeList
     */
    protected $nodeList;

    /**
     * Constructor
     *
     * @param \DOMNodeList $nodeList
     */
    public function __construct(\DOMNodeList $nodeList)
    {
        $this->nodeList = $nodeList;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->nodeList);
    }

    /**
     * @see \IteratorAggregate::getIterator()
     *
     * @return \DOMNodeList
     */
    public function getIterator()
    {
        return $this->nodeList;
    }

    /**
     * Get an array of nodes currently in the NodeList.
     * \DOMNodeList is a dynamic object ; its elements change when you manipulate the DOM.
     *
     * @return array
     */
    public function toArray()
    {
        $nodes = array();
        foreach ($this->nodeList as $node) {
            $nodes[] = $node;
        }

        return $nodes;
    }

    /**
     * @see \DOMNodeList::item()
     *
     * @return \Welldom\Element
     */
    public function item($index)
    {
        return $this->nodeList->item($index);
    }

    /**
     * Count nodes
     *
     * @return int
     */
    public function getLength()
    {
        return $this->nodeList->length;
    }

    /**
     * Remove each node.
     *
     * This NodeList is empty after calling this method.
     *
     * @return void
     */
    public function remove()
    {
        foreach ($this->toArray() as $node) {
            $node->remove();
        }
    }

    /**
     * Rename each node.
     *
     * This NodeList is empty after calling this method.
     * All nodes are removed and replaced by a new one.
     *
     * @param string $name
     * @return void
     */
    public function rename($name)
    {
        foreach ($this->toArray() as $node) {
            $node->rename($name);
        }
    }

    /**
     * Get each names
     *
     * @return array
     */
    public function getName()
    {
        $names = array();
        foreach ($this->nodeList as $node) {
            $names[] = $node->getName();
        }

        return $names;
    }

    /**
     * Get values of each nodes
     *
     * @return array
     */
    public function getValue()
    {
        $values = array();
        foreach ($this->nodeList as $node) {
            $values[] = $node->getValue();
        }

        return $values;
    }

    /**
     * Set value to each nodes
     *
     * @return \Welldom\NodeList
     */
    public function setValue($value)
    {
        foreach ($this->nodeList as $node) {
            $node->setValue($value);
        }

        return $this;
    }

    /**
     * Get inner XML of each node
     *
     * @return array
     */
    public function getInnerXml()
    {
        $xml = array();
        foreach ($this->nodeList as $node) {
            if ($node instanceof Element) {
                $xml[] = $node->getInnerXml();
            }
        }

        return $xml;
    }

    /**
     * Set inner XML to each node
     *
     * @param string $xml XML
     * @return \Welldom\NodeList
     */
    public function setInnerXml($xml)
    {
        foreach ($this->nodeList as $node) {
            if ($node instanceof Element) {
                $node->setInnerXml($xml);
            }
        }

        return $this;
    }

    /**
     * Get XML of each node
     *
     * @return array
     */
    public function getXml()
    {
        $xml = array();
        foreach ($this->nodeList as $node) {
            $xml[] = $node->getXml();
        }

        return $xml;
    }

    /**
     * Magic method, do not use :)
     * The method is called on each node.
     *
     * @return array
     */
    public function __call($method, $arguments)
    {
        $arg = isset($arguments[0]) ? $arguments[0] : null;
        $results = array();
        foreach ($this->toArray() as $node) {
            $results[] = $node->$method($arg);
        }

        return $results;
    }
}
