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
 * XML Errors Handler is an utilitary class to catch libxml errors.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class XmlErrorHandler
{
    static private $userDefinedUseInternalErrors;

    /**
     * Start error handling
     */
    static public function start()
    {
        if (null === self::$userDefinedUseInternalErrors) {
            self::$userDefinedUseInternalErrors = libxml_use_internal_errors(true);
        }
    }

    /**
     * Get last errors
     *
     * @return array
     */
    static public function getErrors()
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        return $errors;
    }

    /**
     * Clean error handling
     */
    static public function clean()
    {
        if (null !== self::$userDefinedUseInternalErrors) {
            libxml_clear_errors();
            libxml_use_internal_errors(self::$userDefinedUseInternalErrors);
            self::$userDefinedUseInternalErrors = null;
        }
    }
}
