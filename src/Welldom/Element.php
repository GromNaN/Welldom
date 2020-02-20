<?php

/*
 * This file is part of the Welldom package.
 *
 * (c) Jérôme Tamarelle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Welldom;

/**
 * DOM Element extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Element extends \DOMElement
{
    /**
     * @return \Welldom\Document
     */
    public function getDocument()
    {
        return $this->ownerDocument;
    }

    /**
     * Get element tag name
     *
     * @return string
     */
    public function getName()
    {
        return $this->nodeName;
    }

    /**
     * Replace the element by one with the given name
     *
     * @param string $nodeName
     * @return \Welldom\Element
     */
    public function rename($nodeName)
    {
        if ($this->getName() == $nodeName) {
            return $this;
        }

        $newNode = $this->ownerDocument->createElement($nodeName);

        if ($this->attributes->length) {
            foreach ($this->attributes as $attribute) {
                $newNode->setAttribute($attribute->nodeName, $attribute->nodeValue);
            }
        }

        while ($child = $this->firstChild) {
            $newNode->appendChild($child);
        }

        if ($this->parentNode) {
            $this->parentNode->replaceChild($newNode, $this);
        }

        return $newNode;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Welldom\NodeList
     */
    public function getElementsByTagName($name)
    {
        return new NodeList(parent::getElementsByTagName($name));
    }

    /**
     * Get element children
     *
     * @return \Welldom\NodeList
     */
    public function getChildNodes()
    {
        return new NodeList($this->childNodes);
    }

    /**
     * Remove the element from its parent.
     *
     * @return \Welldom\Element The removed element
     */
    public function remove()
    {
        if (!$this->parentNode) {
            return $this;
        }

        return $this->parentNode->removeChild($this);
    }


    /**
     * Remove nodes by node name
     *
     * @return array Remaining nodes names
     */
    public function removeChildNodes()
    {
        foreach ($this->childNodes as $childNode) {
            $this->removeChild($childNode);
        }

        return $this;
    }

    /**
     * Get element text value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->nodeValue;
    }

    /**
     * Set element text value
     *
     * @param string $value
     * @return \Welldom\Element
     */
    public function setValue($value)
    {
        $this->nodeValue = $value;

        return $this;
    }

    /**
     * Get all attributes as array
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array();
        foreach ($this->attributes as $name => $attr) {
            $attributes[$name] = $attr->getValue();
        }

        return $attributes;
    }

    /**
     * Set many attributes from an associative array
     *
     * @param array $attributes
     * @param bool $append If FALSE, previous attributes are removed
     */
    public function setAttributes(array $attributes, $append = true)
    {
        if (!$append) {
            $this->removeAttributes();
        }

        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * Removes all attributes
     *
     * @return \Welldom\Element
     */
    public function removeAttributes()
    {
        while ($attr = $this->attributes->item(0)) {
            $this->removeAttributeNode($attr);
        }

        return $this;
    }

    /**
     * Get inner XML
     *
     * @return string
     */
    public function getInnerXml()
    {
        return $this->removeRootTag($this->getXml(), $this->tagName);
    }

    /**
     * Set inner XML
     *
     * @param string $inner
     * @param bool $append If TRUE, previous inner XML is kept
     * @return \Welldom\Element
     */
    public function setInnerXml($inner, $append = false)
    {
        if (false === $append) {
            $this->removeChildNodes();
        }

        if ($inner instanceof \DOMNode) {
            $node = $inner;
            if ($this->ownerDocument->isSameNode($node->ownerDocument)) {
                $node = $node->cloneNode(false);
            } else {
                $node = $this->ownerDocument->importNode($node);
            }
            $this->appendChild($node);

        } elseif ($inner instanceof \DOMNodeList || $inner instanceof NodeList) {
            // We create a temp array because \DOMNodeList is a living object
            $nodes = array();
            foreach ($inner as $node) {
                $nodes[] = $node;
            }
            foreach ($nodes as $node) {
                $this->setInnerXml($node, true);
            }

        } elseif (!empty($inner)) {
            $fragment = $this->ownerDocument->createDocumentFragment();
            if (false === $fragment->appendXml($inner)) {
                throw new \InvalidArgumentException(sprintf('Invalid XML: "%s"', $inner));
            }
            $this->appendChild($fragment);
        }

        return $this;
    }

    /**
     * Get XML representation of the element
     *
     * @return string
     */
    public function getXml()
    {
        return $this->ownerDocument->saveXml($this);
    }

    /**
     * Get array representation of the element
     *
     * @return array
     */
    public function toArray($renderRoot = false)
    {
        $arResult = array();

        if (!$this->hasChildNodes()) {
            if ($renderRoot) {
                return array($this->nodeName => $this->nodeValue);
            }

            return $this->nodeValue;
        }

        foreach ($this->childNodes as $childNode) {
            // how many of these child nodes do we have?
            $childNodeList = $this->getElementsByTagName($childNode->nodeName); // count = 0
            $childCount = 0;
            // there are x number of childs in this node that have the same tag name
            // however, we are only interested in the # of siblings with the same tag name
            foreach ($childNodeList as $oNode) {
                if ($oNode->parentNode->isSameNode($childNode->parentNode)) {
                    $childCount++;
                }
            }

            if ($childNode instanceof self) {
                $mValue = $childNode->toArray(true);
                $mValue = is_array($mValue) ? $mValue[$childNode->nodeName] : $mValue;
            } else {
                $mValue = $childNode->getValue();
            }

            $sKey = $childNode->nodeName[0] == '#' ? 0 : $childNode->nodeName;

            // this will give us a clue as to what the result structure should be
            // how many of these child nodes do we have?
            if ($childCount == 1) {
                // only one child  make associative array
                $arResult[$sKey] = $mValue;
            } else if ($childCount > 1) {
                // more than one child like this  make numeric array
                $arResult[$sKey][] = $mValue;
            } elseif ($childCount == 0) {
                // no child records found, this is DOMText or DOMCDataSection
                $arResult[$sKey] = $mValue;
            }
        }

        // if the child is bar, the result will be array(bar)
        // make the result just 'bar'
        if (count($arResult) == 1 && isset($arResult[0]) && !is_array($arResult[0])) {
            $arResult = $arResult[0];
        }

        if ($renderRoot) {
            return array($this->nodeName => $arResult);
        }

        return $arResult;
    }

    /**
     * Removes the root element from an XML string
     *
     * @ignore
     * @param string $xml
     * @param string $tagName
     * @return string
     */
    protected function removeRootTag($xml, $tagName = '[:alnum:]+')
    {
        return preg_replace(array('/^<' . $tagName . '[^>]*>/', '/<\/' . $tagName . '>$/'), '', $xml);
    }
}

