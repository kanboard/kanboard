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

    /**
     * Load and register a model
     *
     * @static
     * @access public
     * @param  Core\Registry    $registry    DPI container
     * @param  string           $name        Model name
     * @return mixed
     */
    public static function loadModel(Registry $registry, $name)
    {
        if (! isset($registry->$name)) {
            $class = '\Model\\'.ucfirst($name);
            $registry->$name = new $class($registry);
        }

        return $registry->shared($name);
    }

    /**
     * Check if the page is requested through HTTPS
     *
     * Note: IIS return the value 'off' and other web servers an empty value when it's not HTTPS
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isHTTPS()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
    }
}
