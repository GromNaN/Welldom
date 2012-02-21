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

/**
 * @covers \Welldom\Element
 */
class ElementTests extends TestCase
{
// ->getDocument()

    public function testGetDocument()
    {
        $doc = $this->createDocument('<foo><bar>bazbaz</bar></foo>');
        $element = $doc->documentElement->childNodes->item(0);
        $this->assertSame($doc, $element->getDocument(), '->getDocument() returns the owner document');
    }

// ->getElementsByTagName()

    public function testGetElementsByTagName()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz</bar></foo>');
        $elements = $element->getElementsByTagName('bar');
        $this->assertInstanceOf('\Welldom\NodeList', $elements, '->getElementsByTagName() returns an instance of \Welldom\NodeList');
    }

// ->getChildNodes()

    public function testGetChildNodes()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz</bar></foo>');
        $elements = $element->getChildNodes();
        $this->assertInstanceOf('\Welldom\NodeList', $elements, '->getChildNodes() returns an instance of \Welldom\NodeList');
    }

// ->setAttributes()

    public function testGetAttributes()
    {
        $element = $this->createDocumentElement('<foo a="1" b="2"/>');
        $expected = array('a' => '1', 'b' => '2');
        $this->assertEquals($expected, $element->getAttributes());
    }

    public function testSetAttributes()
    {
        $element = $this->createDocumentElement('<foo a="1" b="2"/>');
        $element->setAttributes(array('c' => 3, 'd' => 4));
        $expected = '<foo a="1" b="2" c="3" d="4"/>';
        $this->assertEquals($expected, $element->getXml());
    }

    public function testSetAttributesNotAppends()
    {
        $element = $this->createDocumentElement('<foo a="1" b="2"/>');
        $element->setAttributes(array('c' => 3, 'd' => 4), false);
        $expected = '<foo c="3" d="4"/>';
        $this->assertEquals($expected, $element->getXml());
    }

// ->remove()

    public function testRemove()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz<quz id="lol" /></bar><quz>baz</quz></foo>');
        $removedElement = $element->firstChild->remove();
        $expected = '<foo><quz>baz</quz></foo>';
        $this->assertEquals($expected, $element->getDocument()->getXml(), '->remove() Removes the node from the document');
        $this->assertInstanceOf('\Welldom\Element', $removedElement, '->remove() returns the removed element');
        $this->assertNull($removedElement->parentNode, '->remove() removes the parent node from the element');

        $removedElementTwice = $removedElement->remove();
        $this->assertSame($removedElement, $removedElementTwice, '->remove() do nothing if the node does not have parent');
    }

// ->rename()

    public function testRename()
    {
        $element = $this->createDocumentElement('<foo id="lol" attr="val"><bar id="lol">bazbaz</bar></foo>');
        $this->assertEquals('foo', $element->getName());
        $newElement = $element->rename('quz');
        $this->assertEquals('quz', $newElement->getName());
        $expected = '<quz id="lol" attr="val"><bar id="lol">bazbaz</bar></quz>';
        $this->assertEquals($expected, $element->getDocument()->getXml());
    }

    public function testRenameSame()
    {
        $element = $this->createDocumentElement('<foo/>');
        $newElement = $element->rename('foo');
        $expected = '<foo/>';
        $this->assertSame($element, $newElement, '->rename() keeps the same node if renamed with the same name.');
        $this->assertEquals($expected, $element->getDocument()->getXml());
    }

// ->getInnerXml()

    public function testGetInnerXml()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz</bar><quz a="1"/></foo>');
        $this->assertEquals('<bar>bazbaz</bar><quz a="1"/>', $element->getInnerXml(), '->getInnerXml() returns element inner Xml');
        $this->assertEquals('', $element->childNodes->item(1)->getInnerXml(), '->getInnerXml() returns an empty string if element is empty');
    }

// ->setInnerXml()

    public function testSetInnerXml()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz</bar></foo>');
        $ret = $element->setInnerXml('<quz><bar id="lol" /></quz>', false);
        $this->assertEquals('<foo><quz><bar id="lol"/></quz></foo>', $element->getDocument()->getXml(), '->setInnerXml() replaces element inner XML');
        $this->assertSame($element, $ret, '->setInnerXml() is fluent');
    }

    public function testSetInnerXmlAppends()
    {
        $element = $this->createDocumentElement('<foo><bar>bazbaz</bar></foo>');
        $ret = $element->setInnerXml('<xml/>', true);
        $this->assertEquals('<foo><bar>bazbaz</bar><xml/></foo>', $element->getDocument()->getXml(), '->setInnerXml() appends element inner XML');
        $this->assertSame($element, $ret, '->setInnerXml() is fluent');
    }

    public function testSetInnerXmlWithNode()
    {
        $element = $this->createDocumentElement('<foo/>');
        $innerXml = $element->ownerDocument->createElement('bar');
        $element->setInnerXml($innerXml);
        $this->assertEquals('<foo><bar/></foo>', $element->getXml());
    }

    public function testSetInnerXmlWithNode2()
    {
        $element = $this->createDocumentElement('<foo><bar/><quz a="1"/><quz a="2"/></foo>');
        $innerXml = $element->childNodes->item(1);
        $element->childNodes->item(0)->setInnerXml($innerXml);
        $this->assertEquals('<foo><bar><quz a="1"/></bar><quz a="1"/><quz a="2"/></foo>', $element->getXml());
    }

    public function testSetInnerXmlWithNodeFromOtherDoc()
    {
        $element = $this->createDocumentElement('<foo/>');
        $innerXml = $this->createDocumentElement('<bar/>');
        $element->setInnerXml($innerXml);
        $this->assertEquals('<foo><bar/></foo>', $element->getXml());
    }

    public function testSetInnerXmlWithNodeList()
    {
        $element = $this->createDocumentElement('<foo><bar/><quz a="1"/><quz a="2"/></foo>');
        $innerXml = $element->getElementsByTagName('quz');
        $element->childNodes->item(0)->setInnerXml($innerXml);
        $this->assertEquals('<foo><bar><quz a="1"/><quz a="2"/></bar><quz a="1"/><quz a="2"/></foo>', $element->getXml());
    }

    public function testSetInnerXmlWithNodeListFromOtherDoc()
    {
        $element = $this->createDocumentElement('<foo><bar/><quz a="1"/><quz a="2"/></foo>');
        $innerXml = $this->createDocumentElement('<root><quz a="1"/><quz a="2"/></root>')->childNodes;
        $element->childNodes->item(0)->setInnerXml($innerXml);
        $this->assertEquals('<foo><bar><quz a="1"/><quz a="2"/></bar><quz a="1"/><quz a="2"/></foo>', $element->getXml());
    }

// ->toArray()

    public function testToArray()
    {
        $element = $this->createDocumentElement('<foo id="lol" attr="val"><bar id="lol">bazbaz</bar><bar/></foo>');
        $actual = $element->toArray(true);
        $expected = array(
            'foo' => array(
                'bar' => array('bazbaz', ''),
            ),
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * The owner document is unset until.
     *
     * @param string $source XML source
     * @return \Welldom\Element
     */
    protected function createDocumentElement($source)
    {
        return $this->createDocument($source)->documentElement;
    }
}
