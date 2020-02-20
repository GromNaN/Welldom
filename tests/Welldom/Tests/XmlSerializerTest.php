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
use Welldom\XmlSerializer;

/**
 * @covers \Welldom\XmlSerializer
 */
class XmlSerializerTest extends TestCase
{
    use TestHelpers;

    /**
     * @dataProvider dataForTestXmlToArrayToXml
     */
    public function testXmlToArray($xml, $array)
    {
        $this->assertEquals($array, XmlSerializer::xmlToArray($xml), '::xmlToArray()');
    }

    /**
     * @dataProvider dataForTestXmlToArrayToXml
     */
    public function testArrayToXml($xml, $array)
    {
        $this->assertEquals($xml, XmlSerializer::arrayToXml($array), '::arrayToXml()');
    }

    public function dataForTestXmlToArrayToXml()
    {
        $tabSimple = array('test', 'pipo', '1');

        $tabAsso = array(
            'hello' => 'world',
            '1+1'   => '2',
        );

        return array(
            array(
                '<0>test</0><1>pipo</1><2>1</2>',
                $tabSimple,
            ),
            array(
                '<hello>world</hello><1+1>2</1+1>',
                $tabAsso,
            ),
            array(
                '<simple><0>test</0><1>pipo</1><2>1</2></simple><associative><hello>world</hello><1+1>2</1+1></associative>',
                array('simple' => $tabSimple, 'associative' => $tabAsso),
            ),
        );
    }

}
