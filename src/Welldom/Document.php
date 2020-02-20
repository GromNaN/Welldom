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
 * DOM Document extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class Document extends \DOMDocument
{
    const DEFAULT_ENCODING = 'UTF-8';
    const DEFAULT_VERSION = '1.0';

    /**
     * @var \Welldom\Xpath
     */
    private $xpath = null;

    /**
     * Last loading/validation errors
     *
     * @var array
     */
    private $lastErrors = array();

    /**
     * Constructor
     *
     * @param string $encoding
     * @param string $version
     * @param array $streamOptions
     */
    public function __construct($encoding = null, $version = null)
    {
        if (null === $encoding) {
            $encoding = static::DEFAULT_ENCODING;
        }
        if (null === $version) {
            $version = static::DEFAULT_VERSION;
        }
        parent::__construct($version, $encoding);

        $this->registerNodeClass('DOMDocument', get_called_class());
        $this->registerNodeClass('DOMDocumentFragment', 'Welldom\DocumentFragment');
        $this->registerNodeClass('DOMElement', 'Welldom\Element');
        $this->registerNodeClass('DOMAttr', 'Welldom\Attr');
        $this->registerNodeClass('DOMText', 'Welldom\Text');
        $this->registerNodeClass('DOMComment', 'Welldom\Comment');

        $this->preserveWhiteSpace = false;
        $this->resolveExternals = true;
        $this->substituteEntities = true;
    }

    /**
     * Factory : create a document and load contents.
     *
     * @param string $source
     * @param string $encoding
     * @param string $version
     * @return \Welldom\Document
     */
    static public function create($source, $encoding = null, $version = null)
    {
        $doc = new static($encoding, $version);
        $doc->loadXML($source);

        return $doc;
    }

    /**
     * Factory.
     * Create a document from a DOMNode.
     *
     * @param \Welldom\NodeInterface $node
     * @param bool $deep When false, $node subtree is not imported.
     * @return \Welldom\Document
     */
    static public function createFromNode(\DOMNode $node, $deep = true)
    {
        $doc = new static($node->ownerDocument->encoding, $node->ownerDocument->version);
        $doc->appendChild($doc->importNode($node, $deep));

        return $doc;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->xpath = null;
    }

    /**
     * Get shared Xpath object
     *
     * @return \Welldom\Xpath
     */
    public function getXpath()
    {
        if (null === $this->xpath) {
            $this->xpath = new Xpath($this);
        }

        return $this->xpath;
    }

    /**
     * Get last XML loading errors
     *
     * @return array
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function load($filename, $options = LIBXML_COMPACT)
    {
        $this->xpath = null;

        XmlErrorHandler::start();
        $success = parent::load($filename, $options);
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $success;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function loadXML($source, $options = LIBXML_COMPACT)
    {
        $source = $this->fixDoctype($source);
        $this->xpath = null;

        XmlErrorHandler::start();
        $success = parent::loadXML($source, $options);
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $success;
    }

    /**
     * @param string Inner XML
     */
    public function getInnerXml()
    {
        return $this->documentElement ? $this->documentElement->getInnerXml() : '';
    }

    /**
     * @return string XML
     */
    public function getXml()
    {
        return $this->documentElement ? $this->documentElement->getXml() : '';
    }

    /**
     * {@inheritDoc}
     *
     * The output encoding is the document encoding.
     *      $doc->encoding = 'iso-8859-15';
     *      $doc->saveXML($node);
     */
    public function saveXML(\DOMNode $node = null, $options = null)
    {
        if (null === $node) {
            return parent::saveXML(null, $options);
        }

        if ($node instanceof \DOMDocument) {
            return $node->saveXML();
        }

        $xml = parent::saveXML($node, $options);

        if ('UTF-8' === $encoding = strtoupper($this->encoding)) {
            return $xml;
        }

        return mb_convert_encoding($xml, $encoding, 'UTF-8');
    }

    /**
     * Convert to array
     *
     * @param boolean $renderRoot
     * @return array
     */
    public function toArray($renderRoot = false)
    {
        $array = $this->documentElement->toArray(true);

        if ($renderRoot) {
            $array = array($this->nodeName => $array);
        }

        return $array;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function validate()
    {
        XmlErrorHandler::start();
        $valid = parent::validate();
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $valid;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function schemaValidate($filename)
    {
        XmlErrorHandler::start();
        $valid = parent::schemaValidate($filename);
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $valid;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function schemaValidateSource($source)
    {
        XmlErrorHandler::start();
        $valid = parent::schemaValidateSource($source);
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        return $valid;
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
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function evaluate($expression, $contextNode = null)
    {
        if (null === $contextNode) {
            $contextNode = $this;
        }

        return $this->getXpath()->evaluate($expression, $contextNode);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Welldom\NodeList
     */
    public function query($expression, $contextNode = null)
    {
        if (null === $contextNode) {
            $contextNode = $this;
        }

        return $this->getXpath()->query($expression, $contextNode);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Welldom\Element
     */
    public function queryOne($expression, $contextNode = null)
    {
        if (null === $contextNode) {
            $contextNode = $this;
        }

        return $this->getXpath()->queryOne($expression, $contextNode);
    }

    /**
     * A fix to avoid "unterminated entity reference" error with unescaping string
     * {@inheritDoc}
     *
     * @return \Welldom\Element
     */
    public function createElement($name, $value = null, $namespaceUri = null) {
        $element = new Element($name, null, $namespaceUri);
        $element = $this->importNode($element);
        if (null !== $value) {
            $element->appendChild(new Text($value));
        }

        return $element;
    }

    /**
     * Create node
     *
     * @param string $expression Xpath expression
     * @param string $value
     * @param array $array
     * @return \Welldom\Element
     */
    public function createNode($expression, $value = null, array $attributes = null)
    {
        $current = ('/' === $expression[0]) ? $this : $this->documentElement;
        $xpaths = explode('/', str_replace('//', '', $expression));

        $i = 0;
        foreach ($xpaths as $path) {
            ++$i;
            $nodes = $this->getXpath()->query($path, $current);

            if ($nodes->getLength() > 1) {
                throw new InvalidXpathException(sprintf('Sub-query part "%s" returned more than 1 element', $path));
            }

            if ($nodes->getLength() === 1) {
                $current = $nodes->item(0);

                continue;
            }

            if ($nodes->getLength() === 0) {
                if ('@' === $path[0]) {
                    if ($i !== count($xpaths)) {
                        throw new InvalidXpathException(sprintf('Cannot create the node attribute "%s"', $path));
                    }

                    $path = substr($path, 1);
                    $current = $current->setAttribute($path, $value);

                    continue;
                }

                if ('comment()' === $path) {
                    if ($i !== count($xpaths)) {
                        throw new InvalidXpathException(sprintf('Cannot create the node comment'));
                    }

                    $current = $current->appendChild($this->createComment($value));

                    continue;
                }

                if ('text()' === $path) {
                    if ($i !== count($xpaths)) {
                        throw new InvalidXpathException(sprintf('Cannot create the node text'));
                    }

                    $current = $current->appendChild($this->createTextNode($value));

                    continue;
                }

                if (strpos($path, '[')) {
                    if (!preg_match('/^(\w*)\[@(\w*)="(\w*)"\]$/', $path, $matches)) {
                        throw new InvalidXpathException(sprintf('Cannot create sub-part query "%s"', $path));
                    }

                    $current = $current->appendChild($this->createElement($matches[1]));
                    $current->setAttribute($matches[2], $matches[3]);
                } else {
                    $current = $current->appendChild($this->createElement($path));
                }
            }
        }

        if (null !== $value) {
            $current->nodeValue = $value;
        }

        if ($attributes && XML_ELEMENT_NODE === $current->nodeType) {
            $current->setAttributes($attributes);
        }

        return $current;
    }

    /**
     * Get node
     *
     * @param string $expression
     * @param boolean $create
     * @return \Welldom\Element
     */
    public function getNode($expression, $create = false)
    {
        $node = $this->getXpath()->queryOne($expression);

        if (false === $node && true === $create) {
            $node = $this->createNode($expression);
        }

        return $node;
    }

    /**
     * Get the value of a node
     *
     * @param string $expression Xpath expression
     * @param mixed $default Default value
     * @return mixed Node value
     */
    public function getNodeValue($expression, $default = false)
    {
        if (false === $node = $this->getXpath()->queryOne($expression)) {
            return $default;
        }

        return $node->getValue();
    }

    /**
     * Set the value of a node.
     * Create the node if needed.
     *
     * @param string $expression
     * @param string $value
     * @return \Welldom\NodeInterface
     */
    public function setNodeValue($expression, $value = null)
    {
        if (false === $node = $this->getXpath()->queryOne($expression)) {
            return false;
        }

        return $node->setValue($value);
    }

    /**
     * Get values of a list of nodes
     *
     * @param string $expression Xpath expression
     * @return array
     */
    public function getNodeListValues($expression)
    {
        return $this->getXpath()->query($expression)->getValue();
    }

    /**
     * Get the Xml representation of a node
     *
     * @param string $expression Xpath expression
     * @param bool $throwException Throws an exception if number of nodes found is not 1
     * @return mixed Node value
     */
    public function getNodeXml($expression, $default = false)
    {
        if (false === $node = $this->getXpath()->queryOne($expression)) {
            return $default;
        }

        return $node->getXml();
    }

    /**
     * Get Xml representation of a list of nodes
     *
     * @param string $expression Xpath expression
     * @return array Node Xml
     */
    public function getNodeListXml($expression)
    {
        return $this->getXpath()->query($expression)->getXml();
    }

    /**
     * Get the Xml representation of an inner node
     *
     * @param string $expression
     * @param string $default
     * @return string
     */
    public function getNodeInnerXml($expression, $default = false)
    {
        if (false === $node = $this->getXpath()->queryOne($expression)) {
            return $default;
        }

        return $node->getInnerXml();
    }

    /**
     * Set the inner Xml of a node
     *
     * @param string $expression
     * @param string $xml
     * @param boolean $append
     * @return \Welldom\NodeInterface
     */
    public function setNodeInnerXml($expression, $xml, $append = false)
    {
        if (false === $node = $this->createNode($expression)) {
            return false;
        }

        return $node->setInnerXml($xml, $append);
    }

    /**
     * Had doctype to the XML source if absent
     *
     * @param string $xml
     * @return string
     */
    protected function fixDoctype($xml)
    {
        if ('<?xml' === substr($xml, 0, 5)) {
            return $xml;
        }

        return sprintf('<?xml version="%s" encoding="%s" ?>', $this->version, $this->encoding) . $xml;
    }
}
