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

trait TestHelpers
{
    /**
     * @param string $source XML source
     * @return \Welldom\Document
     */
    protected function createDocument($source)
    {
        $doc = new Document();

        if (!empty($source)) {
            $doc->loadXML($source);
        }

        return $doc;
    }
}
