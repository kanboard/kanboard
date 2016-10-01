<?php

namespace Kanboard\Core;

/**
 * Template
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Helper\AppHelper               $app
 * @property \Kanboard\Helper\AssetHelper             $asset
 * @property \Kanboard\Helper\DateHelper              $dt
 * @property \Kanboard\Helper\FileHelper              $file
 * @property \Kanboard\Helper\FormHelper              $form
 * @property \Kanboard\Helper\HookHelper              $hook
 * @property \Kanboard\Helper\ModelHelper             $model
 * @property \Kanboard\Helper\SubtaskHelper           $subtask
 * @property \Kanboard\Helper\TaskHelper              $task
 * @property \Kanboard\Helper\TextHelper              $text
 * @property \Kanboard\Helper\UrlHelper               $url
 * @property \Kanboard\Helper\UserHelper              $user
 * @property \Kanboard\Helper\LayoutHelper            $layout
 * @property \Kanboard\Helper\ProjectHeaderHelper     $projectHeader
 */
class Template
{
    /**
     * Helper object
     *
     * @access private
     * @var Helper
     */
    private $helper;

    /**
     * List of template overrides
     *
     * @access private
     * @var array
     */
    private $overrides = array();

    /**
     * Template constructor
     *
     * @access public
     * @param  Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Expose helpers with magic getter
     *
     * @access public
     * @param  string $helper
     * @return mixed
     */
    public function __get($helper)
    {
        return $this->helper->getHelper($helper);
    }

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
     * Core template: 'task/show' or 'kanboard:task/show'
     * Plugin template: 'myplugin:task/show'
     *
     * @access public
     * @param  string  $template
     * @return string
     */
    public function getTemplateFile($template)
    {
        $plugin = '';
        $template = isset($this->overrides[$template]) ? $this->overrides[$template] : $template;

        if (strpos($template, ':') !== false) {
            list($plugin, $template) = explode(':', $template);
        }

        if ($plugin !== 'kanboard' && $plugin !== '') {
            return implode(DIRECTORY_SEPARATOR, array(PLUGINS_DIR, ucfirst($plugin), 'Template', $template.'.php'));
        }

        return implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Template', $template.'.php'));
    }
}
