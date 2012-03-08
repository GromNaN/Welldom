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

use Welldom\XsltProcessor;
use Welldom\Document;

/**
 * @covers \Welldom\XsltProcessor
 */
class XsltProcessorTest extends TestCase
{

// ->__construct()

    /**
     * @dataProvider dataForTestConstructor
     */
    public function testConstructor($filename, $errorsCount)
    {
        $xslt = new XsltProcessor(FILES_DIR . $filename);

        $this->assertInternalType('array', $xslt->getLastErrors());
        $this->assertCount($errorsCount, $xslt->getLastErrors());
    }

    public function dataForTestConstructor()
    {
        return array(
            array('/frameworks.xsl', 0),
            array('/invalid.xml', 6),
            array('/frameworks-invalid.xsl', 5),
        );
    }

// ->transformToXml()

    public function testTransformToXml()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/valid.xsl');
        $xslt->setParameters(array('foo' => 'bar'));
        $doc = new Document();
        $doc->load(FILES_DIR . '/valid.xml');
        $xml = $xslt->transformToXml($doc, array('owner' => 'Me', 'foo' => 'quz'));

        $this->assertEquals('<movies><owner>Me</owner><movie>The Matrix</movie><movie>Titanic</movie><movie>The Sixth Sense</movie></movies>', $xml, '->transformToXml() returns the generated XML');
        $this->assertFalse($xslt->getParameter('param'), '->transformToXml() reset parameters');
        $this->assertEquals('bar', $xslt->getParameter('foo'), '->transformToXml() reset parameters');
        $this->assertCount(0, $xslt->getLastErrors(), '->transformToXml() generates no error');
    }

    public function testTransformToXmlError()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/invalid.xsl');
        $this->assertEquals(array(), $xslt->getLastErrors());
        $doc = new Document();
        $doc->load(FILES_DIR . '/valid.xml');
        $xml = $xslt->transformToXml($doc, array('param' => 'val'));

        $this->assertFalse($xslt->getParameter('param'), '->transformToXml() reset parameters');
        $this->assertCount(3, $xslt->getLastErrors(), '->transformToXml() generate last errors');
    }

// ->transformToDoc()

    public function testTransformToDoc()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/valid.xsl');
        $xslt->setParameters(array('foo' => 'bar'));
        $doc = new Document();
        $doc->load(FILES_DIR . '/valid.xml');
        $genDoc = $xslt->transformToDoc($doc, array('owner' => 'Me'));

        $this->assertInstanceOf('\Welldom\Document', $genDoc, '->transformToDoc() returns an Welldom\Document');
        $this->assertInstanceOf('\Welldom\Element', $genDoc->childNodes->item(0));
        $this->assertEquals('<movies><owner>Me</owner><movie>The Matrix</movie><movie>Titanic</movie><movie>The Sixth Sense</movie></movies>', $genDoc->getXml(), '->transformToDoc() returns the generated XML');
        $this->assertFalse($xslt->getParameter('param'), '->transformToDoc() reset parameters');
        $this->assertEquals('bar', $xslt->getParameter('foo'), '->transformToDoc() reset parameters');
        $this->assertCount(0, $xslt->getLastErrors(), '->transformToDoc() generates no error');
    }

    public function testTransformToDocError()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/invalid.xsl');
        $this->assertEquals(array(), $xslt->getLastErrors());
        $doc = new Document();
        $doc->load(FILES_DIR . '/valid.xml');
        $xml = $xslt->transformToDoc($doc, array('param' => 'val'));

        $this->assertInstanceOf('\Welldom\Document', $doc, '->transformToDoc() returns Document in case of warning');
        $this->assertFalse($xslt->getParameter('param'), '->transformToDoc() reset parameters');
        $this->assertCount(3, $xslt->getLastErrors(), '->transformToDoc() generate last errors');
    }

// ->transformToUri()

    public function testTransformToUri()
    {
        $filename = tempnam(FILES_DIR, 'tmp_');
        unlink($filename);

        $xslt = new XsltProcessor(FILES_DIR . '/valid.xsl');
        $xslt->setParameters(array('foo' => 'bar'));
        $doc = new Document();
        $doc->load(FILES_DIR . '/valid.xml');
        $ret = $xslt->transformToUri($doc, $filename, array('owner' => 'Me'));

        $this->assertTrue(is_file($filename), '->transformToUri() creates a file');
        $this->assertEquals('<movies><owner>Me</owner><movie>The Matrix</movie><movie>Titanic</movie><movie>The Sixth Sense</movie></movies>'."\n", file_get_contents($filename), '->transformToUri() returns the generated XML');
        $this->assertFalse($xslt->getParameter('param'), '->transformToUri() reset parameters');
        $this->assertEquals('bar', $xslt->getParameter('foo'), '->transformToUri() reset parameters');
        $this->assertCount(0, $xslt->getLastErrors(), '->transformToUri() generates no error');
        unlink($filename);
    }

// ->setParameters()

    public function testSetParameters()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/valid.xsl');
        $xslt->setParameters(array(
            'foo' => 'fooval',
            'bar' => 'barval',
        ));

        $this->assertSame('fooval', $xslt->getParameter('foo'));
        $this->assertSame('barval', $xslt->getParameter('bar'));
    }

// ->removeParameters()

    public function testRemoveParameters()
    {
        $xslt = new XsltProcessor(FILES_DIR . '/valid.xsl');
        $xslt->setParameter('foo', 'fooval');
        $xslt->setParameter('bar', 'barval');
        $xslt->removeParameters(array('foo', 'bar'));

        $this->assertFalse($xslt->getParameter('foo'));
        $this->assertFalse($xslt->getParameter('bar'));
    }
}
