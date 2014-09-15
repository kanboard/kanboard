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
     * List of paths
     *
     * @access private
     * @var    array
     */
    private $paths = array();

    /**
     * Load the missing class
     *
     * @access public
     * @param  string   $class   Class name with namespace
     */
    public function load($class)
    {
        foreach ($this->paths as $path) {

            $filename = $path.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';

            if (file_exists($filename)) {
                require $filename;
                break;
            }
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

    /**
     * Register a new path
     *
     * @access public
     * @param  string    $path  Path
     * @return Core\Loader
     */
    public function setPath($path)
    {
        $this->paths[] = $path;
        return $this;
    }
}
