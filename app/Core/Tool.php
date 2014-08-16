<?php

namespace Core;

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

    public static function loadModel(Registry $registry, $name)
    {
        if (! isset($registry->$name)) {
            $class = '\Model\\'.ucfirst($name);
            $registry->$name = new $class($registry);
        }

        return $registry->shared($name);
    }
}
