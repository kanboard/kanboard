<?php

namespace Kanboard\Core;

use Pimple\Container;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Tool class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Tool
{
    /**
     * Remove recursively a directory
     *
     * @static
     * @access public
     * @param  string $directory
     * @param  bool   $removeDirectory
     */
    public static function removeAllFiles($directory, $removeDirectory = true)
    {
        $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        if ($removeDirectory) {
            rmdir($directory);
        }
    }

    /**
     * Build dependency injection containers from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     * @return Container
     */
    public static function buildDIC(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\Kanboard\\'.$namespace.'\\'.$name;
                $container[lcfirst($name)] = function ($c) use ($class) {
                    return new $class($c);
                };
            }
        }

        return $container;
    }

    /**
     * Build dependency injection container from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     * @return Container
     */
    public static function buildFactories(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\Kanboard\\'.$namespace.'\\'.$name;
                $container[lcfirst($name)] = $container->factory(function ($c) use ($class) {
                    return new $class($c);
                });
            }
        }

        return $container;
    }

    /**
     * Build dependency injection container for custom helpers from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     * @return Container
     */
    public static function buildDICHelpers(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\Kanboard\\'.$namespace.'\\'.$name;
                $container['helper']->register($name, $class);
            }
        }

        return $container;
    }
}
