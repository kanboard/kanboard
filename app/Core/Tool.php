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
     * Get the mailbox hash from an email address
     *
     * @static
     * @access public
     * @param  string  $email
     * @return string
     */
    public static function getMailboxHash($email)
    {
        if (! strpos($email, '@') || ! strpos($email, '+')) {
            return '';
        }

        list($local_part,) = explode('@', $email);
        list(,$identifier) = explode('+', $local_part);

        return $identifier;
    }

    /**
     * Build dependency injection container from an array
     *
     * @static
     * @access public
     * @param  Container  $container
     * @param  array      $namespaces
     */
    public static function buildDIC(Container $container, array $namespaces)
    {
        foreach ($namespaces as $namespace => $classes) {
            foreach ($classes as $name) {
                $class = '\\'.$namespace.'\\'.$name;
                $container[lcfirst($name)] = function ($c) use ($class) {
                    return new $class($c);
                };
            }
        }
    }

    /**
     * Generate a jpeg thumbnail from an image
     *
     * @static
     * @access public
     * @param  string    $src_file         Source file image
     * @param  string    $dst_file         Destination file image
     * @param  integer   $resize_width     Desired image width
     * @param  integer   $resize_height    Desired image height
     */
    public static function generateThumbnail($src_file, $dst_file, $resize_width = 250, $resize_height = 100)
    {
        $metadata = getimagesize($src_file);
        $src_width = $metadata[0];
        $src_height = $metadata[1];
        $dst_y = 0;
        $dst_x = 0;

        if (empty($metadata['mime'])) {
            return;
        }

        if ($resize_width == 0 && $resize_height == 0) {
            $resize_width = 100;
            $resize_height = 100;
        }

        if ($resize_width > 0 && $resize_height == 0) {
            $dst_width = $resize_width;
            $dst_height = floor($src_height * ($resize_width / $src_width));
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        }
        elseif ($resize_width == 0 && $resize_height > 0) {
            $dst_width = floor($src_width * ($resize_height / $src_height));
            $dst_height = $resize_height;
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
        }
        else {

            $src_ratio = $src_width / $src_height;
            $resize_ratio = $resize_width / $resize_height;

            if ($src_ratio <= $resize_ratio) {
                $dst_width = $resize_width;
                $dst_height = floor($src_height * ($resize_width / $src_width));

                $dst_y = ($dst_height - $resize_height) / 2 * (-1);
            }
            else {
                $dst_width = floor($src_width * ($resize_height / $src_height));
                $dst_height = $resize_height;

                $dst_x = ($dst_width - $resize_width) / 2 * (-1);
            }

            $dst_image = imagecreatetruecolor($resize_width, $resize_height);
        }

        switch ($metadata['mime']) {
            case 'image/jpeg':
            case 'image/jpg':
                $src_image = imagecreatefromjpeg($src_file);
                break;
            case 'image/png':
                $src_image = imagecreatefrompng($src_file);
                break;
            case 'image/gif':
                $src_image = imagecreatefromgif($src_file);
                break;
            default:
                return;
        }

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
        imagejpeg($dst_image, $dst_file);
        imagedestroy($dst_image);
    }
}
