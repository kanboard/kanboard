<?php

namespace Core;

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
    const PATH = 'app/Templates/';

    /**
     * Load a template
     *
     * Example:
     *
     * $template->load('template_name', ['bla' => 'value']);
     *
     * @access public
     * @return string
     */
    public function load()
    {
        if (func_num_args() < 1 || func_num_args() > 2) {
            die('Invalid template arguments');
        }

        if (! file_exists(self::PATH.func_get_arg(0).'.php')) {
            die('Unable to load the template: "'.func_get_arg(0).'"');
        }

        if (func_num_args() === 2) {

            if (! is_array(func_get_arg(1))) {
                die('Template variables must be an array');
            }

            extract(func_get_arg(1));
        }

        ob_start();

        include self::PATH.func_get_arg(0).'.php';

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
