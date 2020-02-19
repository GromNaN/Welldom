<?php

/*
 * This file is part of the Welldom package.
 *
 * (c) Groupe Express Roularta
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Welldom\Tests;

use PHPUnit\Framework\TestCase;
use Welldom\XsltProcessorCollection;

/**
 * @covers \Welldom\XsltProcessorCollection
 */
class XsltProcessorCollectionTest extends TestCase
{
    use TestHelpers;

    public function testGetXsltProcessor()
    {
        $filename = FILES_DIR . '/frameworks.xsl';
        $xslt = XsltProcessorCollection::getXsltProcessor($filename);
        $this->assertInstanceOf('\Welldom\XsltProcessor', $xslt);
        $this->assertSame($xslt, XsltProcessorCollection::getXsltProcessor($filename), '::getXsltProcessor() keep in memory');
        XsltProcessorCollection::free();
    }

    public function testGetXsltProcessorLoadException()
    {
        $this->expectException(\DOMException::class);
        XsltProcessorCollection::getXsltProcessor(FILES_DIR . '/frameworks-invalid.xsl');
    }

    public function testGetXsltProcessorFileException()
    {
        $this->expectException(\InvalidArgumentException::class);
        XsltProcessorCollection::getXsltProcessor(FILES_DIR . '/does-not-exists.xsl');
    }
}
