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

use Welldom\Document;

trait TestHelpers
{
    /**
     * @param string $source XML source
     * @return \Welldom\Document
     */
    private function createDocument($source)
    {
        $doc = new Document();

        if (!empty($source)) {
            $doc->loadXML($source);
        }

        return $doc;
    }

    private static function fixtureFile(string $filename) :string
    {
        return dirname(__DIR__, 2).'/_files'.$filename;
    }
}
