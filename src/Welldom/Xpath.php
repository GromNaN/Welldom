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

use Welldom\Exception\InvalidXpathException;

/**
 * DOM Xpath extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Xpath extends \DOMXPath
{
    /**
     * Evaluate Xpath expression.
     *
     * @param string $expression    The XPath expression to execute.
     * @param \DOMNode $contextnode
     * @param bool $registerNodeNS
     * @return mixed
     * @throws \Welldom\Exception\InvalidXpathException
     */
    public function evaluate($expression, \DOMNode $contextnode = null, $registerNodeNS = true)
    {
        XmlErrorHandler::start();
        $results = parent::evaluate($expression, $contextnode);
        XmlErrorHandler::clean();

        if (false === $results) {
            throw new InvalidXpathException(sprintf('Invalid Xpath expression "%s".', $expression));
        }

        if ($results instanceof \DOMNodeList) {
            return new NodeList($results);
        }

        return $results;
    }

    /**
     * Query Xpath expression.
     *
     * @param string $expression     The XPath expression to execute.
     * @param \DOMNode $contextnode
     * @param bool $registerNodeNS
     * @return \Welldom\NodeList
     * @throws \Welldom\Exception\InvalidXpathException
     */
    public function query($expression, \DOMNode $contextnode = null, $registerNodeNS = true)
    {
        XmlErrorHandler::start();
        $results = parent::query($expression, $contextnode, $registerNodeNS);
        XmlErrorHandler::clean();

        if (false === $results) {
            throw new InvalidXpathException(sprintf('Invalid Xpath expression "%s".', $expression));
        }

        return new NodeList($results);
    }

    /**
     * Execute Xpath query and retrieve 1 result
     *
     * @param string $expression Xpath expression
     * @param \DOMNode $contextnode
     * @param bool $registerNodeNS
     * @return \Welldom\NodeInterface|false The node found or FALSE if the number of results is not 1
     * @throws \Welldom\Exception\InvalidXpathException
     */
    public function queryOne($expression, \DOMNode $contextnode = null, $registerNodeNS = true)
    {
        XmlErrorHandler::start();
        $results = parent::query($expression, $contextnode, $registerNodeNS);
        XmlErrorHandler::clean();

        if (false === $results) {
            throw new InvalidXpathException(sprintf('Invalid Xpath expression "%s".', $expression));
        }

        if (1 === $results->length) {
            return $results->item(0);
        }

        return false;
    }
}
