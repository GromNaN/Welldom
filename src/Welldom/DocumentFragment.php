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

use Welldom\Exception\XmlLoadingException;

/**
 * DOM Document Fragment extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class DocumentFragment extends \DOMDocumentFragment
{
    protected $lastErrors = array();

    /**
     * Get last XML loading errors.
     *
     * @return array
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }

    /**
     * {@inheritDoc}
     */
    public function appendXml($xml)
    {
        XmlErrorHandler::start();
        $success = parent::appendXml($xml);
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $success;
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
}
