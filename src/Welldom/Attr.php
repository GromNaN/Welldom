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
 * DOM Attribute extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Attr extends \DOMAttr implements NodeInterface
{
    /**
     * Removes the attribute from its parent.
     *
     * @return \Welldom\Attr
     */
    public function remove()
    {
        $this->ownerElement->removeAttribute($this->nodeName);
    }

    /**
     * Rename the attribute
     *
     * @return \Welldom\Attr The new attribute
     */
    public function rename($name)
    {
        $ownerElement = $this->ownerElement;
        $ownerElement->removeAttributeNode($this);
        $ownerElement->setAttribute($name, $this->value);

        return $ownerElement->getAttribute($name);
    }

    /**
     * Get attribute value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return \Welldom\Attr
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->nodeName;
    }

    /**
     * Get XML representation
     *
     * @return string
     */
    public function getXml()
    {
        return $this->ownerDocument->saveXML($this);
    }
}
