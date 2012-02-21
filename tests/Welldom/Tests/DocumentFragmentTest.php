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
 * @covers \Welldom\DocumentFragment
 */
class DocumentFragmentTest extends TestCase
{
    public function testAppendXmlError()
    {
        $doc = $this->createDocument('<foo />');
        $fragment = $doc->createDocumentFragment();

        $errors = $fragment->getLastErrors();
        $this->assertInternalType('array', $errors);
        $this->assertCount(0, $errors);

        $success = $fragment->appendXML('<invalid att="v><test></invalid>');
        $this->assertSame(false, $success);

        $errors = $fragment->getLastErrors();
        $this->assertInternalType('array', $errors);
        $this->assertCount(5, $errors);
    }

    public function testGetChildNodes()
    {
        $doc = $this->createDocument('<foo />');
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML('<bar>bazbaz</bar>');
        $nodeList = $fragment->getChildNodes();
        $this->assertInstanceOf('\Welldom\NodeList', $nodeList);
    }

}
