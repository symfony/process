<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process;

/**
 * An executable finder specifically designed for the PHP executable.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PhpExecutableFinder
{
    private $executableFinder;

    public function __construct()
    {
        $this->executableFinder = new ExecutableFinder();
    }

    /**
     * Finds The PHP executable.
     *
     * @param bool $includeArgs Whether or not include command arguments
     *
     * @return string|false The PHP executable path or false if it cannot be found
     */
    public function find($includeArgs = true)
    {
        // HHVM support
        if (defined('HHVM_VERSION')) {
            return (getenv('PHP_BINARY') ?: PHP_BINARY).($includeArgs ? ' '.implode(' ', $this->findArguments()) : '');
        }

        // Check PHP PATH environment variable
        if ($php = getenv('PHP_PATH')) {

            // check if PHP_PATH is executable (even with options ex: path/to/php -c something)
            if (!is_executable(substr($php, 0, strrpos($php, ' ')))){
                return false;
            }

            return $php;
        }

        // PHP_BINARY return the current sapi executable
        if (defined('PHP_BINARY') && PHP_BINARY && in_array(PHP_SAPI, array('cli', 'cli-server')) && is_file(PHP_BINARY)) {
            return PHP_BINARY;
        }

        if ($php = getenv('PHP_PEAR_PHP_BIN')) {
            if (is_executable($php)) {
                return $php;
            }
        }

        $dirs = array(PHP_BINDIR);
        if ('\\' === DIRECTORY_SEPARATOR) {
            $dirs[] = 'C:\xampp\php\\';
        }

        return $this->executableFinder->find('php', false, $dirs);
    }

    /**
     * Finds the PHP executable arguments.
     *
     * @return array The PHP executable arguments
     */
    public function findArguments()
    {
        $arguments = array();

        // HHVM support
        if (defined('HHVM_VERSION')) {
            $arguments[] = '--php';
        }

        return $arguments;
    }
}
