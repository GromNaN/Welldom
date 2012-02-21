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

use Welldom\Xpath;

/**
 * @covers \Welldom\Xpath
 */
class XpathTest extends TestCase
{

    /**
     * @covers Welldom\Xpath::queryOne
     */
    public function testQueryOne()
    {
        $xpath = $this->getXpath('<foo><bar>bazbaz</bar><baz/><baz/></foo>');
        $this->assertInstanceOf('\DOMNode', $xpath->queryOne('//foo/bar'), '->queryOne() returns the corresponding node');
        $this->assertSame(false, $xpath->queryOne('//foo/quz'), '->queryOne() returns FALSE if not found');
        $this->assertSame(false, $xpath->queryOne('//foo/baz'), '->queryOne() returns FALSE if too many');
    }

    /**
     * @covers Welldom\Xpath::queryOne
     * @expectedException \Welldom\Exception\InvalidXpathException
     */
    public function testQueryOneException()
    {
        $this->getXpath('<foo/>')->queryOne('foo[');
    }

    /**
     * @covers Welldom\Xpath::query
     */
    public function testQuery()
    {
        $xpath = $this->getXpath('<foo><bar>bazbaz</bar><baz/><baz/></foo>');
        $this->assertInstanceOf('\Welldom\NodeList', $xpath->query('//bar'), '->query() returns a custom NodeList if 1 result');
        $this->assertInstanceOf('\Welldom\NodeList', $xpath->query('//quz'), '->query() returns a custom NodeList if no result');
        $this->assertInstanceOf('\Welldom\NodeList', $xpath->query('//baz'), '->query() returns a custom NodeList if multiple results');
    }

    /**
     * @covers Welldom\Xpath::query
     * @expectedException \Welldom\Exception\InvalidXpathException
     */
    public function testQueryException()
    {
        $this->getXpath('<foo/>')->query('foo[');
    }

    /**
     * @covers Welldom\Xpath::evaluate
     */
    public function testEvaluate()
    {
        $xpath = $this->getXpath('<foo><bar>bazbaz</bar><baz/><baz/></foo>');
        $this->assertInstanceOf('\Welldom\NodeList', $xpath->evaluate('//bar'), '->query() returns a custom NodeList');
        $this->assertSame('bazbaz', $xpath->evaluate('string(//bar)'), '->query() returns the evaluated expression');
    }

    /**
     * @covers Welldom\Xpath::evaluate
     * @expectedException \Welldom\Exception\InvalidXpathException
     */
    public function testEvaluateException()
    {
        $this->getXpath('<foo/>')->evaluate('foo[');
    }

    /**
     * @param string $source XML Source
     * @return \Welldom\Xpath
     */
    protected function getXpath($source)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($source);

        return new Xpath($doc);
    }
}
