<?php

namespace Core;

use LogicException;

/**
 * Template class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Template
{
    /**
     * Template path
     *
     * @var string
     */
    const PATH = 'app/Template/';

    /**
     * Load a template
     *
     * Example:
     *
     * $template->load('template_name', ['bla' => 'value']);
     *
     * @access public
     * @params string   $__template_name   Template name
     * @params array    $__template_args   Key/Value map of template variables
     * @return string
     */
    public function load($__template_name, array $__template_args = array())
    {
        $__template_file = self::PATH.$__template_name.'.php';

        if (! file_exists($__template_file)) {
            throw new LogicException('Unable to load the template: "'.$__template_name.'"');
        }

        extract($__template_args);

        ob_start();
        include $__template_file;
        return ob_get_clean();
    }

    /**
     * Render a page layout
     *
     * @access public
     * @param  string   $template_name   Template name
     * @param  array    $template_args   Key/value map
     * @param  string   $layout_name     Layout name
     * @return string
     */
    public function layout($template_name, array $template_args = array(), $layout_name = 'layout')
    {
        return $this->load(
            $layout_name,
            $template_args + array('content_for_layout' => $this->load($template_name, $template_args))
        );
    }
}
