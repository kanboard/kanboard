<?php

namespace Core;

use Pimple\Container;

/**
 * Tool class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Tool
{
    /**
     * Write a CSV file
     *
     * @static
     * @access public
     * @param  array    $rows       Array of rows
     * @param  string   $filename   Output filename
     */
    public static function csv(array $rows, $filename = 'php://output')
    {
        $fp = fopen($filename, 'w');

        if (is_resource($fp)) {

            foreach ($rows as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);
        }
    }

    /**
     * Load and register a model
     *
     * @static
     * @access public
     * @param  Pimple\Container    $container     Container instance
     * @param  string              $name          Model name
     * @return mixed
     */
    public static function loadModel(Container $container, $name)
    {
        if (! isset($container[$name])) {
            $class = '\Model\\'.ucfirst($name);
            $container[$name] = new $class($container);
        }

        return $container[$name];
    }
}
