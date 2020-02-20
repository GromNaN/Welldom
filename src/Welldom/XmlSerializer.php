<?php

/*
 * This file is part of the Welldom package.
 *
 * (c) Jérôme Tamarelle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Welldom;

/**
 * Xml Serializer.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class XmlSerializer
{
    /**
     * Serialize an array into an XML
     *
     * @param array $array
     * @return string
     */
    static public function arrayToXml(array $array)
    {
        $xml = '';
        foreach ($array as $name => $content) {
            if (is_array($content)) {
                $content = self::arrayToXml($content);
            }

            $xml .= sprintf('<%s>%s</%s>', $name, $content, $name);
        }

        return $xml;
    }

    /**
     * Unserialize an XML into an array.
     * Attributes are node serialized.
     *
     * @param string $xml
     * @return array
     */
    static public function xmlToArray($xml)
    {
        $pattern = '/^<(.*?)>(.*?)<\/\1>(.*)/';

        if (preg_match($pattern, $xml)) {
            $array = array();
            while ($xml && preg_match($pattern, $xml, $matches)) {
                $array[$matches[1]] = self::xmlToArray($matches[2]);
                $xml = $matches[3];
            }

            return $array;
        }

        return $xml;
    }
}
