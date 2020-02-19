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

class NodeListTest extends TestCase
{
    use TestHelpers;

// ->getIterator()

    public function testIterator()
    {
        $doc = $this->createDocument('<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>');
        $nodeList = $doc->getElementsByTagName('bar');

        $count = 0;
        foreach ($nodeList as $node) {
            $this->assertInstanceOf('\DOMNode', $node);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

// ->getLength()

    public function testGetLength()
    {
        $doc = $this->createDocument('<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>');
        $nodeList = $doc->getElementsByTagName('bar');

        $this->assertEquals(2, $nodeList->getLength());
    }

// ->item()

    public function testItem()
    {
        $doc = $this->createDocument('<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>');
        $nodeList = $doc->getElementsByTagName('bar');

        $this->assertInstanceOf('\DOMNode', $nodeList->item(0));
        $this->assertNull($nodeList->item(2));
    }

// ->remove()

    /**
     * @dataProvider dataForTestRemove
     */
    public function testRemove($xml, $xpath, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $nodeList = $doc->query($xpath);
        $nodeList->remove();
        $this->assertEquals($expected, $doc->getXml(), $message);
    }

    public function dataForTestRemove()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar',
                '<foo><baz>baz</baz></foo>',
                '->remove() Removes all elements',
            ),
            2 => array(
                '<foo><bar a="1"/><bar a="2"/><bar a="3"/></foo>',
                '//foo/bar/@a',
                '<foo><bar/><bar/><bar/></foo>',
                '->remove() Removes all attributes',
            ),
            3 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                '<foo><bar><!-- comment --></bar><baz>baz</baz><bar/></foo>',
                '->remove() Removes all texts',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/comment()',
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '->remove() Removes all comments',
            ),
        );
    }

// ->rename()

    /**
     * @dataProvider dataForTestRename
     */
    public function testRename($xml, $xpath, $name, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $nodeList = $doc->query($xpath);
        $nodeList->rename($name);
        $this->assertEquals($expected, $doc->getXml(), $message);
    }

    public function dataForTestRename()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar',
                'quz',
                '<foo><quz>bar 1</quz><baz>baz</baz><quz>bar 2</quz></foo>',
                '->remove() Renames all elements',
            ),
            2 => array(
                '<foo><bar a="1"/><bar a="2"/><bar a="3"/></foo>',
                '//foo/bar/@a',
                'lol',
                '<foo><bar lol="1"/><bar lol="2"/><bar lol="3"/></foo>',
                '->remove() Renames all attributes',
            ),
        );
    }

// ->getName()

    /**
     * @dataProvider dataForTestGetName
     */
    public function testGetName($xml, $xpath, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $actual = $doc->query($xpath)->getName();
        $this->assertEquals($expected, $actual, $message);
    }

    public function dataForTestGetName()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/*',
                array('bar', 'baz', 'bar'),
                '->getName() Get names of each element',
            ),
            2 => array(
                '<foo><bar a="1" b="2">bar 1</bar><bar c="x">bar 2</bar></foo>',
                '//foo/bar/@*',
                array('a', 'b', 'c'),
                '->getName() Get names of each attributes',
            ),
            3 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                array('#text', '#text'),
                '->getName() Get names of each text',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar><!-- comment 2 -->bar 2</bar></foo>',
                '//foo/bar/comment()',
                array('#comment', '#comment'),
                '->getName() Get names of each comment',
            ),
        );
    }

// ->getXml()

    /**
     * @dataProvider dataForTestGetXml
     */
    public function testGetXml($xml, $xpath, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $actual = $doc->query($xpath)->getXml();
        $this->assertEquals($expected, $actual, $message);
    }

    public function dataForTestGetXml()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/*',
                array('<bar>bar 1</bar>', '<baz>baz</baz>', '<bar>bar 2</bar>'),
                '->getName() Get xml of each element',
            ),
            2 => array(
                '<foo><bar a="1" b="2">bar 1</bar><bar c="x">bar 2</bar></foo>',
                '//foo/bar/@*',
                array(' a="1"', ' b="2"', ' c="x"'),
                '->getName() Get xml of each attributes',
            ),
            3 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                array('bar 1', 'bar 2'),
                '->getName() Get xml of each text',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar><!-- comment 2 -->bar 2</bar></foo>',
                '//foo/bar/comment()',
                array('<!-- comment -->', '<!-- comment 2 -->'),
                '->getName() Get xml of each comment',
            ),
        );
    }

// ->getNodePath()

    /**
     * @dataProvider dataForTestGetNodePath
     */
    public function testGetNodePath($xml, $xpath, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $actual = $doc->query($xpath)->getNodePath();
        $this->assertEquals($expected, $actual, $message);
    }

    public function dataForTestGetNodePath()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/*',
                array('/foo/bar[1]', '/foo/baz', '/foo/bar[2]'),
                '->getName() Get xml of each element',
            ),
            2 => array(
                '<foo><bar a="1" b="2">bar 1</bar><bar c="x">bar 2</bar></foo>',
                '//foo/bar/@*',
                array('/foo/bar[1]/@a', '/foo/bar[1]/@b', '/foo/bar[2]/@c'),
                '->getName() Get xml of each attributes',
            ),
            3 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                array('/foo/bar[1]/text()', '/foo/bar[2]/text()'),
                '->getName() Get xml of each text',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar><!-- comment 2 -->bar 2</bar></foo>',
                '//foo/bar/comment()',
                array('/foo/bar[1]/comment()', '/foo/bar[2]/comment()'),
                '->getName() Get xml of each comment',
            ),
        );
    }

// ->getValue()

    /**
     * @dataProvider dataForTestGetValue
     */
    public function testGetValue($xml, $xpath, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $actual = $doc->query($xpath)->getValue();
        $this->assertEquals($expected, $actual, $message);
    }

    public function dataForTestGetValue()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar',
                array('bar 1', 'bar 2'),
                '->getValue() Get values of each element',
            ),
            2 => array(
                '<foo><bar a="1"/><bar a="2"/><bar a="3"/></foo>',
                '//foo/bar/@a',
                array('1', '2', '3'),
                '->getValue() Get values of each attribute',
            ),
            3 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                array('bar 1', 'bar 2'),
                '->getValue() Get values of each attribute',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><baz>baz</baz><bar><!-- comment 2 -->bar 2</bar></foo>',
                '//foo/bar/comment()',
                array(' comment ', ' comment 2 '),
                '->getValue() Get values of each comment',
            ),
        );
    }

// ->setValue()

    /**
     * @dataProvider dataForTestSetValue
     */
    public function testSetValue($xml, $xpath, $value, $expected, $message)
    {
        $doc = $this->createDocument($xml);
        $ret = $doc->query($xpath)->setValue($value);
        $this->assertEquals($expected, $doc->getXml(), $message);
        $this->assertInstanceOf('\Welldom\NodeList', $ret, '->setValue() is fluent');
    }

    public function dataForTestSetValue()
    {
        return array(
            1 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar',
                'plume',
                '<foo><bar>plume</bar><baz>baz</baz><bar>plume</bar></foo>',
                '->setValue() Set the same value for each element',
            ),
            2 => array(
                '<foo><bar a="1" b="2">bar 1</bar><bar c="x">bar 2</bar></foo>',
                '//foo/bar/@*',
                'plume',
                '<foo><bar a="plume" b="plume">bar 1</bar><bar c="plume">bar 2</bar></foo>',
                '->setValue() Set the same value for each attribute',
            ),
            3 => array(
                '<foo><bar>bar 1</bar><baz>baz</baz><bar>bar 2</bar></foo>',
                '//foo/bar/text()',
                'plume',
                '<foo><bar>plume</bar><baz>baz</baz><bar>plume</bar></foo>',
                '->setValue() Set the same value for each attribute',
            ),
            4 => array(
                '<foo><bar>bar 1<!-- comment --></bar><bar><!-- comment 2 -->bar 2</bar></foo>',
                '//foo/bar/comment()',
                'plume',
                '<foo><bar>bar 1<!--plume--></bar><bar><!--plume-->bar 2</bar></foo>',
                '->getValue() Get values of each comment',
            ),
        );
    }

// ->getInnerXml()

    public function testGetInnerXml()
    {
        $doc = $this->createDocument('<foo><bar>text 1</bar><bar>text 2<!-- comment --></bar></foo>');
        $innerXml = $doc->getElementsByTagName('bar')->getInnerXml();
        $expected = array('text 1', 'text 2<!-- comment -->');
        $this->assertEquals($expected, $innerXml, '->setInnerXml() get inner XML of each element');
    }

// ->setInnerXml()

    public function testSetInnerXml()
    {
        $doc = $this->createDocument('<foo><bar/><bar/></foo>');
        $nodeList = $doc->getElementsByTagName('bar');
        $ret = $nodeList->setInnerXml('<quz/>text');
        $this->assertEquals('<foo><bar><quz/>text</bar><bar><quz/>text</bar></foo>', $doc->getXml(), '->setInnerXml() set inner XML of each element');
        $this->assertSame($nodeList, $ret, '->setInnerXml() is fluent');
    }

}
