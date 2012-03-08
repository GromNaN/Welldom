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
 * DOM XsltProcessor extension.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class XsltProcessor
{
    /**
     * @var \XSLTProcessor
     */
    protected $processor;

    /**
     * @var array
     */
    private $lastErrors = array();

    /**
     * @var array
     */
    private $replacedParameters;

    /**
     * Constructor.
     * Import the given XSL.
     *
     * @param string $filename XSL file path
     */
    public function __construct($filename)
    {
        XmlErrorHandler::start();

        $this->processor = new \XSLTProcessor();
        $xslDoc = new \DOMDocument();
        $xslDoc->load($filename, LIBXML_NOCDATA | LIBXML_COMPACT);
        $this->processor->registerPHPFunctions();
        $this->processor->importStyleSheet($xslDoc);

        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();
    }

    /**
     * Transforms the given node using the XSL and returns the generated XML.
     *
     * @param \DOMNode $node
     * @param array $parameters
     * @return string The generated XML or FALSE in case of error
     */
    public function transformToXml(\DOMNode $node, array $parameters = array())
    {
        $this->preTransform($parameters);
        $xml = $this->processor->transformToXml($node);
        $this->postTransform($parameters);

        if (false === $xml) {
            return false;
        }

        return rtrim($xml, "\r\n ");
    }

    /**
     * Transforms the given node using the XSL and returns the generated DOM document.
     *
     * @param \DOMNode $node
     * @param array $parameters
     * @return \Welldom\Document The generated document or FALSE in case of error
     */
    public function transformToDoc(\DOMNode $node, array $parameters = array())
    {
        $this->preTransform($parameters);
        $doc = $this->processor->transformToDoc($node);
        $this->postTransform();

        if (false === $doc) {
            return false;
        }

        // XSLTProcessor::transformToDoc() returns a DOMDocument, not \Welldom\Document
        // @link https://bugs.php.net/bug.php?id=53693
        return Document::createFromNode($doc->documentElement, true);
    }

    /**
     * Transforms the given node using the XSL and saves the result to the given filename.
     *
     * @param \DOMNode $node
     * @param string $filename
     * @param array $parameters
     * @return mixed The number of byte writter or FALSE in case of error
     */
    public function transformToUri(\DOMNode $node, $filename, array $parameters = array())
    {
        $this->preTransform($parameters);
        $ret = $this->processor->transformToUri($node, $filename);
        $this->postTransform($parameters);

        return $ret;
    }

    /**
     * Get a parameter value
     *
     * @param string $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->processor->getParameter('', $name);
    }

    /**
     * Set a parameter value
     *
     * @param string $name
     * @param string $value
     * @return \Welldom\XsltProcessor
     */
    public function setParameter($name, $value)
    {
        return $this->processor->setParameter('', $name, $value);

        return $this;
    }

    /**
     * Set a list of parameters
     *
     * @param array $parameters Associative array (name, value)
     * @return \Welldom\XsltProcessor
     */
    public function setParameters(array $parameters)
    {
        $this->processor->setParameter('', $parameters);

        return $this;
    }

    /**
     * Remove a parameter
     *
     * @param string $name
     * @return \Welldom\XsltProcessor
     */
    public function removeParameter(array $name)
    {
        $this->processor->removeParameter('', $name);

        return $this;
    }

    /**
     * Removes a list of parameters.
     *
     * @param type $names List of names
     * @return \Welldom\XsltProcessor
     */
    public function removeParameters(array $names)
    {
        foreach ($names as $name) {
            $this->processor->removeParameter('', $name);
        }

        return $this;
    }

    /**
     * Get last transformation errors
     *
     * @return array
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }

    /**
     * Method called before any transformation.
     * Handle errors and set parameters.
     *
     * @param array $parameters Transformation specific parameters
     */
    protected function preTransform(array $parameters)
    {
        if (0 !== count($parameters)) {
            $this->replacedParameters = array();
            foreach ($parameters as $name => $value) {
                $this->replacedParameters[$name] = $this->processor->getParameter('', $name);
                $this->processor->setParameter('', $name, $value);
            }
        }

        XmlErrorHandler::start();
    }

    /**
     * Method called after any transformation.
     * Handle errors and reset parameters.
     */
    protected function postTransform()
    {
        $this->lastErrors = XmlErrorHandler::getErrors();
        XmlErrorHandler::clean();

        if (0 !== count($this->replacedParameters))
        {
            foreach ($this->replacedParameters as $name => $value) {
                if (false === $value) {
                    $this->processor->removeParameter('', $name);
                } else {
                    $this->processor->setParameter('', $name, $value);
                }
            }
        }
    }
}
