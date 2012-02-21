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
 * DOM Text extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Text extends \DOMText implements NodeInterface
{
    /**
     * Removes the node from its parent.
     *
     * @return \Welldom\Text
     */
    public function remove()
    {
        return $this->parentNode->removeChild($this);
    }

    /**
     * Get node value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->wholeText;
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
