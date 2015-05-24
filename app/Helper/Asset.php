<?php

namespace Helper;

/**
 * Assets helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Asset extends \Core\Base
{
    /**
     * Add a Javascript asset
     *
     * @param  string   $filename   Filename
     * @return string
     */
    public function js($filename)
    {
        return '<script type="text/javascript" src="'.$filename.'?'.filemtime($filename).'"></script>';
    }

    /**
     * Add a stylesheet asset
     *
     * @param  string   $filename   Filename
     * @param  boolean  $is_file    Add file timestamp
     * @param  string   $media      Media
     * @return string
     */
    public function css($filename, $is_file = true, $media = 'screen')
    {
        return '<link rel="stylesheet" href="'.$filename.($is_file ? '?'.filemtime($filename) : '').'" media="'.$media.'">';
    }

    /**
     * Get custom css
     *
     * @access public
     * @return string
     */
    public function customCss()
    {
        if ($this->config->get('application_stylesheet')) {
            return '<style>'.$this->config->get('application_stylesheet').'</style>';
        }

        return '';
    }
}
