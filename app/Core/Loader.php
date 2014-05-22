<?php

namespace Core;

/**
 * Loader class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Loader
{
    /**
     * Load the missing class
     *
     * @access public
     * @param  string   $class   Class name
     */
    public function load($class)
    {
        $filename = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';

        if (file_exists($filename)) {
            require $filename;
        }
    }

    /**
     * Register the autoloader
     *
     * @access public
     */
    public function execute()
    {
        spl_autoload_register(array($this, 'load'));
    }
}
