<?php
/*
 * This file is part of the Welldom package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple autoloader that follow the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more informations.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    if (0 === strpos($className, 'Welldom')) {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if (is_file($fileName)) {
            require $fileName;
            return true;
        }
    }
    return false;
});
