<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Layout helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Layout extends Base
{
    /**
     * Render a template without the layout if Ajax request
     *
     * @access public
     * @param  string $template Template name
     * @param  array  $params   Template parameters
     * @return string
     */
    public function app($template, array $params = array())
    {
        if ($this->request->isAjax()) {
            return $this->template->render($template, $params);
        }

        $params['board_selector'] = $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId());
        return $this->template->layout($template, $params);
    }
}
