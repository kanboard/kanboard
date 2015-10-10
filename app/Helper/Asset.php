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
    public function js($filename, $async = false)
    {
        return '<script '.($async ? 'async' : '').' type="text/javascript" src="'.$this->helper->url->dir().$filename.'?'.filemtime($filename).'"></script>';
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
        return '<link rel="stylesheet" href="'.$this->helper->url->dir().$filename.($is_file ? '?'.filemtime($filename) : '').'" media="'.$media.'">';
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

    /**
     * Get CSS for task colors
     *
     * @access public
     * @return string
     */
    public function colorCss()
    {
        return '<style>'.$this->color->getCss().'</style>';
    }
}
