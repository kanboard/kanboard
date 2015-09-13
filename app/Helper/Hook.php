<?php

namespace Helper;

/**
 * Template Hook helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Hook extends \Core\Base
{
    private $hooks = array();

    /**
     * Render all attached hooks
     *
     * @access public
     * @param  string  $hook
     * @param  array   $variables
     * @return string
     */
    public function render($hook, array $variables = array())
    {
        $buffer = '';

        foreach ($this->hooks as $name => $template) {
            if ($hook === $name) {
                $buffer .= $this->template->render($template, $variables);
            }
        }

        return $buffer;
    }

    /**
     * Attach a template to a hook
     *
     * @access public
     * @param  string  $hook
     * @param  string  $template
     * @return \Helper\Hook
     */
    public function attach($hook, $template)
    {
        $this->hooks[$hook] = $template;
        return $this;
    }
}
