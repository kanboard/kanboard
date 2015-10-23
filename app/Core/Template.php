<?php

namespace Kanboard\Core;

/**
 * Template class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Template extends Helper
{
    /**
     * List of template overrides
     *
     * @access private
     * @var array
     */
    private $overrides = array();

    /**
     * Render a template
     *
     * Example:
     *
     * $template->render('template_name', ['bla' => 'value']);
     *
     * @access public
     * @param  string   $__template_name   Template name
     * @param  array    $__template_args   Key/Value map of template variables
     * @return string
     */
    public function render($__template_name, array $__template_args = array())
    {
        extract($__template_args);

        ob_start();
        include $this->getTemplateFile($__template_name);
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
        return $this->render(
            $layout_name,
            $template_args + array('content_for_layout' => $this->render($template_name, $template_args))
        );
    }

    /**
     * Define a new template override
     *
     * @access public
     * @param  string  $original_template
     * @param  string  $new_template
     */
    public function setTemplateOverride($original_template, $new_template)
    {
        $this->overrides[$original_template] = $new_template;
    }

    /**
     * Find template filename
     *
     * Core template name: 'task/show'
     * Plugin template name: 'myplugin:task/show'
     *
     * @access public
     * @param  string  $template_name
     * @return string
     */
    public function getTemplateFile($template_name)
    {
        $template_name = isset($this->overrides[$template_name]) ? $this->overrides[$template_name] : $template_name;

        if (strpos($template_name, ':') !== false) {
            list($plugin, $template) = explode(':', $template_name);
            $path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'plugins';
            $path .= DIRECTORY_SEPARATOR.ucfirst($plugin).DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.$template.'.php';
        } else {
            $path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.$template_name.'.php';
        }

        return $path;
    }
}
