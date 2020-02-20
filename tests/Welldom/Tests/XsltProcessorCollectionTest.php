<?php

/*
 * This file is part of the Welldom package.
 *
 * (c) Jérôme Tamarelle
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
        $filename = self::fixtureFile('/frameworks.xsl');
        $xslt = XsltProcessorCollection::getXsltProcessor($filename);
        $this->assertInstanceOf('\Welldom\XsltProcessor', $xslt);
        $this->assertSame($xslt, XsltProcessorCollection::getXsltProcessor($filename), '::getXsltProcessor() keep in memory');
        XsltProcessorCollection::free();
    }

    public function testGetXsltProcessorLoadException()
    {
        $this->expectException(\DOMException::class);
        XsltProcessorCollection::getXsltProcessor(self::fixtureFile('/frameworks-invalid.xsl'));
    }

    public function testGetXsltProcessorFileException()
    {
        $this->expectException(\InvalidArgumentException::class);
        XsltProcessorCollection::getXsltProcessor(self::fixtureFile('/does-not-exists.xsl'));
    }
}
