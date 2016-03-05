<?php

namespace Kanboard\Helper;

use Kanboard\Core\Markdown;
use Kanboard\Core\Base;

/**
 * Text Helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class TextHelper extends Base
{
    /**
     * HTML escaping
     *
     * @param  string   $value    Value to escape
     * @return string
     */
    public function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Markdown transformation
     *
     * @param  string    $text     Markdown content
     * @param  array     $link     Link parameters for replacement
     * @return string
     */
    public function markdown($text, array $link = array())
    {
        $parser = new Markdown($this->container, $link);
        $parser->setMarkupEscaped(MARKDOWN_ESCAPE_HTML);
        return $parser->text($text);
    }

    /**
     * Format a file size
     *
     * @param  integer  $size        Size in bytes
     * @param  integer  $precision   Precision
     * @return string
     */
    public function bytes($size, $precision = 2)
    {
        $base = log($size) / log(1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision).$suffixes[(int)floor($base)];
    }

    /**
     * Get the number of bytes from PHP size
     *
     * @param  integer  $val        PHP size (example: 2M)
     * @return integer
     */
    public function phpToBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);

        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Return true if needle is contained in the haystack
     *
     * @param  string   $haystack   Haystack
     * @param  string   $needle     Needle
     * @return boolean
     */
    public function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Return a value from a dictionary
     *
     * @param  mixed   $id              Key
     * @param  array   $listing         Dictionary
     * @param  string  $default_value   Value displayed when the key doesn't exists
     * @return string
     */
    public function in($id, array $listing, $default_value = '?')
    {
        if (isset($listing[$id])) {
            return $this->helper->text->e($listing[$id]);
        }

        return $default_value;
    }
}
