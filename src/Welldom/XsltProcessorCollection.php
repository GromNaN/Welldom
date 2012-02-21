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
 * XSLT Processor repository.
 *
 * Creates and store them in memory to reduce calls to XsltProcessor::importStylesheet method.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class XsltProcessorCollection
{
    /**
     * @var array
     */
    static private $xsltProcessors = array();

    /**
     * Get an XSLT processor for the given XSL stylesheet.
     *
     * @param string $filename XSL file name
     * @return \XSLTProcessor
     */
    static public function getXsltProcessor($filename)
    {
        if (false === $realpath = realpath($filename)) {
            throw new \InvalidArgumentException(sprintf('XSL file "%s" does not exists', $filename));
        }

        if (!isset(self::$xsltProcessors[$realpath])) {
            $xslt = new XsltProcessor($filename);

            if ($errors = $xslt->getLastErrors()) {
                throw new \DOMException("Error while loading XSL stylesheet: \n" . implode("\n", $errors));
            }

            self::$xsltProcessors[$realpath] = $xslt;
        }

        return self::$xsltProcessors[$realpath];
    }

    /**
     * Unset all XsltProcessors.
     */
    public function free()
    {
        self::$xsltProcessors = array();
    }
}
