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
 * Interface DOM Node extensions.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
interface NodeInterface
{
    function remove();

    function getValue();

    function setValue($value);

    function getName();

    function getXml();
}
