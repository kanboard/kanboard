<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Template Hook helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class HookHelper extends Base
{
    /**
     * Add assets JS or CSS
     *
     * @access public
     * @param  string  $type
     * @param  string  $hook
     * @return string
     */
    public function asset($type, $hook)
    {
        $buffer = '';

        foreach ($this->hook->getListeners($hook) as $file) {
            $buffer .= $this->helper->asset->$type($file);
        }

        return $buffer;
    }

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

        foreach ($this->hook->getListeners($hook) as $template) {
            $buffer .= $this->template->render($template, $variables);
        }

        return $buffer;
    }

    /**
     * Attach a template to a hook
     *
     * @access public
     * @param  string  $hook
     * @param  string  $template
     * @return \Kanboard\Helper\Hook
     */
    public function attach($hook, $template)
    {
        $this->hook->on($hook, $template);
        return $this;
    }
}
