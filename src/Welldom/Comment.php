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
 * DOM Comment extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Comment extends \DOMComment implements NodeInterface
{
    /**
     * Removes the node from its parent.
     *
     * @return \Welldom\Comment
     */
    public function remove()
    {
        if (!$this->parentNode) {
            return $this;
        }

        return $this->parentNode->removeChild($this);
    }

    /**
     * Get node value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->data;
    }

    /**
     * Set node value
     *
     * @param string $value
     * @return \Welldom\Comment
     */
    public function setValue($value)
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Get node name
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
