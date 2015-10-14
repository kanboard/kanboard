<?php

namespace Kanboard\Helper;

/**
 * Board Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Board extends \Kanboard\Core\Base
{
    /**
     * Return true if tasks are collapsed
     *
     * @access public
     * @param  integer   $project_id
     * @return boolean
     */
    public function isCollapsed($project_id)
    {
        return $this->userSession->isBoardCollapsed($project_id);
    }
}
