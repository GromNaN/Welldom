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

use Welldom\Document;

/**
 * @covers \Welldom\Document
 */
class DocumentTest extends TestCase
{
    public function testConstructorEncoding()
    {
        $doc = Document::create('<foo />', 'UTF-8');

        $expected = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<foo/>'."\n";
        $this->assertEquals($expected, $doc->saveXml());

        $doc->encoding = 'ISO-8859-15';
        $expected = '<?xml version="1.0" encoding="ISO-8859-15"?>'."\n".'<foo/>'."\n";
        $this->assertEquals($expected, $doc->saveXml());
    }

// ::createFromNode()

    public function testCreateFromNode()
    {
        $doc = $this->createDocument('<foo><bar>richard</bar></foo>');
        $node = $doc->documentElement->getChildNodes()->item(0);

        $newDoc = Document::createFromNode($node);

        $this->assertEquals('<bar>richard</bar>', $newDoc->getXml(), '->fromNode()');
    }

// ->load()

    public function testLoad()
    {
        $doc = new Document();
        $success = $doc->load(FILES_DIR . '/valid.xml');
        $this->assertTrue($success, '->load() returns true');
    }

    public function testLoadError()
    {
        $doc = new Document();
        $success = $doc->load(FILES_DIR . '/invalid.xml');
        $this->assertFalse($success, '->load() returns false');
    }

    public function testLoadUnsetXpath()
    {
        $doc = new Document();
        $doc->getXpath();
        $doc->load(FILES_DIR . '/valid.xml');
        $this->assertEquals(3, $doc->getXpath()->query('//movie')->getLength(), 'The Xpath is built on last loaded XML');
    }

// ->loadXML()

    public function testLoadXML()
    {
        $doc = new Document();
        $success = $doc->loadXML('<foo><bar/></foo>');
        $this->assertTrue($success, '->loadXML() returns true');
    }

    public function testLoadXMLError()
    {
        $doc = new Document();
        $this->assertEquals(array(), $doc->getLastErrors());

        $this->assertFalse($doc->loadXML(''), '->loadXML() returns false with empty string');
        $this->assertInternalType('array', $doc->getLastErrors());
        $this->assertCount(1, $doc->getLastErrors());

        $this->assertFalse($doc->loadXML('<foo><bar></foo a="1>'), '->loadXML() returns false with invalid XML');
        $this->assertInternalType('array', $doc->getLastErrors());
        $this->assertCount(3, $doc->getLastErrors());
    }

    public function testLoadXMLUnsetXpath()
    {
        $doc = new Document();
        $doc->getXpath();
        $doc->loadXML('<foo><bar/></foo>');
        $this->assertEquals(1, $doc->getXpath()->query('//bar')->getLength(), 'The Xpath is built on last loaded XML');
    }

// ->getXmlWithoutDoctype()

    public function testGetXml()
    {
        $xml = '<foo><bar/></foo>';
        $doc = $this->createDocument($xml);
        $this->assertEquals($xml, $doc->getXml());
    }

    public function testGetXmlEmpty()
    {
        $doc = new Document();
        $this->assertEquals('', $doc->getXml());
    }

    public function testGetXmlAfterAnError()
    {
        $doc = new Document();
        try {
            $doc->loadXML('<invalid>');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('', $doc->getXml());
        }
    }

// ->toArray()

    /**
     * @dataProvider dataForTestToArray
     */
    public function testToArray($xml, $root, $expected)
    {
        $array = Document::create($xml)->toArray($root);
        $this->assertEquals($expected, $array, '->toArray()');
    }

    public function dataForTestToArray()
    {
        return array(
            array(
                '<bar>bazbaz</bar>',
                false,
                array('bar' => 'bazbaz')
            ),
            array(
                '<foo><bar>bazbaz</bar></foo>',
                false,
                array('foo' => array('bar' => 'bazbaz'))
            ),
            array(
                '<foo><bar>bazbaz</bar></foo>',
                true,
                array('#document' => array('foo' => array('bar' => 'bazbaz')))
            ),
            array(
                '<foo><bar>bazbaz</bar><bar>bliz</bar></foo>',
                true,
                array('#document' => array('foo' => array('bar' => array('bazbaz', 'bliz'))))
            ),
        );
    }

// ->getElementsByTagName()

    public function testGetElementsByTagName()
    {
        $doc = Document::create('<foo><bar>bazbaz</bar></foo>');
        $nodes = $doc->getElementsByTagName('bar');
        $this->assertInstanceOf('\Welldom\NodeList', $nodes, '->getElementsByTagName() returns an instance of \Welldom\NodeList');
    }

// ->createNode()

    /**
     * @dataProvider dataForTestCreateNode
     */
    public function testCreateNode($xml, $expression, $content, $expected, $expectedNodeClass, $message)
    {
        $doc = Document::create($xml);
        $node = $doc->createNode($expression, $content);
        $this->assertEquals($expected, $doc->getXml(), $message);
        $this->assertInstanceOf($expectedNodeClass, $node, '->createNode() returns the created node');
    }

    public function dataForTestCreateNode()
    {
        return array(
            1 => array(
                '<foo></foo>',
                'content',
                null,
                '<foo><content/></foo>',
                '\Welldom\Element',
                '->createNode() created the node with the right name'
            ),
            2 => array(
                '<foo></foo>',
                'content',
                12,
                '<foo><content>12</content></foo>',
                '\Welldom\Element',
                '->createNode() created the node with the right name and the right value'
            ),
            3 => array(
                '<foo></foo>',
                'content/@id',
                null,
                '<foo><content id=""/></foo>',
                '\Welldom\Attr',
                '->createNode() created attribute with the right name'
            ),
            4 => array(
                '<foo><content id=""/></foo>',
                'content/@bar',
                'foo',
                '<foo><content id="" bar="foo"/></foo>',
                '\Welldom\Attr',
                '->createNode() created attribute with the right name and value'
            ),
            5 => array(
                '<foo><content id="" bar="foo"/></foo>',
                'content/subcontent/subsubcontent',
                null,
                '<foo><content id="" bar="foo"><subcontent><subsubcontent/></subcontent></content></foo>',
                '\Welldom\Element',
                '->createNode() created node recursively with the right name'
            ),
            6 => array(
                '<foo><content id="" bar="foo"><subcontent><subsubcontent/></subcontent></content></foo>',
                'content/sscontent/subcontent',
                null,
                '<foo><content id="" bar="foo"><subcontent><subsubcontent/></subcontent><sscontent><subcontent/></sscontent></content></foo>',
                '\Welldom\Element',
                '->createNode() created node recursively with the right name'
            ),
            7 => array(
                '<foo></foo>',
                'content[@id="12"]',
                null,
                '<foo><content id="12"/></foo>',
                '\Welldom\Element',
                '->createNode() created node with attribute value with the right name'
            ),
            8 => array(
                '<foo><content id="12"/><content id="13"/></foo>',
                'content[@id="12"]/url',
                null,
                '<foo><content id="12"><url/></content><content id="13"/></foo>',
                '\Welldom\Element',
                '->createNode() created node with attribute value with the right name'
            ),
            9 => array(
                '<foo><content/></foo>',
                'content/comment()',
                'my comment',
                '<foo><content><!--my comment--></content></foo>',
                '\Welldom\Comment',
                '->createNode() created comment node'
            ),
            10 => array(
                '<foo><content/></foo>',
                'content/comment()',
                null,
                '<foo><content><!----></content></foo>',
                '\Welldom\Comment',
                '->createNode() created empty comment node'
            ),
            11 => array(
                '<foo><content/></foo>',
                'content/text()',
                'my text',
                '<foo><content>my text</content></foo>',
                '\Welldom\Text',
                '->createNode() created comment node'
            ),
            12 => array(
                '<foo><content/></foo>',
                'content/text()',
                null,
                '<foo><content></content></foo>',
                '\Welldom\Text',
                '->createNode() created empty comment node'
            ),
        );
    }

// ->getNode()

    /**
     * @dataProvider dataForTestGetNode
     */
    public function testGetNode($xml, $xpath, $create, $expectedXml, $expectedNode, $message)
    {
        $doc = $this->createDocument($xml);
        $node = $doc->getNode($xpath, $create);
        $this->assertEquals($expectedXml, $doc->getXml(), $message);
        $this->assertEquals($expectedNode, $node->getXml(), $message);
    }

    public function dataForTestGetNode()
    {
        return array(
            1 => array(
                '<foo><bar>bazbaz</bar></foo>',
                '//foo/bar',
                false,
                '<foo><bar>bazbaz</bar></foo>',
                '<bar>bazbaz</bar>',
                '->getNode() returns the found node',
            ),
            2 => array(
                '<foo><bar>bazbaz</bar></foo>',
                '//foo/bar',
                true,
                '<foo><bar>bazbaz</bar></foo>',
                '<bar>bazbaz</bar>',
                '->getNode() returns the found node',
            ),
            3 => array(
                '<foo><bar>bazbaz</bar></foo>',
                '//foo/quz/bar',
                true,
                '<foo><bar>bazbaz</bar><quz><bar/></quz></foo>',
                '<bar/>',
                '->getNode() creates and returns the node',
            ),
        );
    }

    public function testGetNodeError()
    {
        $xml = '<foo><bar>bazbaz</bar></foo>';
        $doc = $this->createDocument($xml);
        $success = $doc->getNode('//foo/quz/bar', false);
        $this->assertFalse($success, '->getNode() returns FALSE when not found and not created');
        $this->assertEquals($xml, $doc->getXml(), '->getNode() does not modify the document in case of error');
    }

// ->getNodeValue()

    public function testGetNodeValue()
    {
        $doc = Document::create('<foo><bar><slip>my bar!</slip></bar></foo>');

        $this->assertEquals('my bar!', $doc->getNodeValue('//slip'), '->getNodeValue() returns the value of the node');
    }

    public function testGetNodeValueDefault()
    {
        $doc = Document::create('<foo><bar><slip>my bar!</slip></bar></foo>');

        $this->assertEquals('default', $doc->getNodeValue('//blablabla', 'default'), '->getNodeValue() returns the default if the node is not found');
    }

// ->getNodeListValues()

    public function testGetNodeListValues()
    {
        $doc = Document::create('<foo><bar><slip>my foo!</slip><slip>my bar!</slip></bar></foo>');
        $list = $doc->getNodeListValues('//slip');
        $expected = array('my foo!', 'my bar!');
        $this->assertEquals($expected, $list, '->getNodeValue() returns an array of node values');
    }

    public function testGetNodeListValuesEmpty()
    {
        $doc = Document::create('<foo><bar><slip>my foo!</slip><slip>my bar!</slip></bar></foo>');
        $list = $doc->getNodeListValues('//blablabla');
        $expected = array();
        $this->assertEquals($expected, $list, '->getNodeValue() returns an empty array if 0 node is found');
    }

// ->getNodeXml()

    public function testGetNodeXml()
    {
        $doc = Document::create('<foo><bar>bazbaz</bar></foo>');

        $this->assertEquals('<bar>bazbaz</bar>', $doc->getNodeXml('//bar'));
    }

    public function testGetNodeXmlDefault()
    {
        $doc = Document::create('<foo><bar>bazbaz</bar></foo>');

        $this->assertEquals('default', $doc->getNodeXml('//blablabla', 'default'));
    }

// ->getNodeListXml()

    public function testGetNodeListXml()
    {
        $doc = Document::create('<foo><bar><slip>my foo!</slip><slip>my bar!</slip></bar></foo>');
        $actual = $doc->getNodeListXml('//slip');
        $expected = array('<slip>my foo!</slip>', '<slip>my bar!</slip>');
        $this->assertEquals($expected, $actual, '->getNodeListXml() returns an array of node xml');
    }

    public function testGetNodeListXmlEmpty()
    {
        $doc = Document::create('<foo><bar><slip>my foo!</slip><slip>my bar!</slip></bar></foo>');
        $actual = $doc->getNodeListXml('//blablabla');
        $expected = array();
        $this->assertEquals($expected, $actual, '->getNodeListXml() returns an empty array if 0 node is found');
    }

// ->getNodeInnerXml()

    public function testGetNodeInnerXml()
    {
        $doc = Document::create('<foo><bar><slip>my bar!</slip></bar></foo>');

        $this->assertEquals('<slip>my bar!</slip>', $doc->getNodeInnerXml('//bar'), '->getNodeInnerXml() returns the inner xml of a node');
    }

    public function testGetNodeInnerXmlDefault()
    {
        $doc = Document::create('<foo><bar><slip>my foo!</slip><slip>my bar!</slip></bar></foo>');

        $this->assertEquals('default', $doc->getNodeInnerXml('//blablabla', 'default'), '->getNodeInnerXml() returns the default value if 0 node is found');
    }

// ->saveXML()

    public function testSaveXML()
    {
        $xml = utf8_decode('<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<root>Jérôme</root>'."\n");
        $doc = $this->createDocument($xml);
        $this->assertEquals($xml, $doc->saveXML($doc), '->saveXML() respects the encoding');

        $expected = utf8_decode('<root>Jérôme</root>');
        $this->assertEquals($expected, $doc->saveXML($doc->documentElement), '->saveXML() respects the encoding');

        $xml = utf8_decode('<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<root><foo><bar>Jérôme</bar></foo></root>'."\n");
        $doc = $this->createDocument($xml);
        $expected = utf8_decode('<bar>Jérôme</bar>');
        $this->assertEquals($expected, $doc->queryOne('//bar')->getXml(), '->saveXML() respects the encoding');
    }
}
