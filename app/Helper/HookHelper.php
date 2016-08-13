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

        foreach ($this->hook->getListeners($hook) as $params) {
            $buffer .= $this->helper->asset->$type($params['template']);
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

        foreach ($this->hook->getListeners($hook) as $params) {
            if (! empty($params['variables'])) {
                $variables = array_merge($variables, $params['variables']);
            }

            $buffer .= $this->template->render($params['template'], $variables);
        }

        return $buffer;
    }

    /**
     * Attach a template to a hook
     *
     * @access public
     * @param  string $hook
     * @param  string $template
     * @param  array  $variables
     * @return $this
     */
    public function attach($hook, $template, array $variables = array())
    {
        $this->hook->on($hook, array(
            'template' => $template,
            'variables' => $variables,
        ));

        return $this;
    }
}
