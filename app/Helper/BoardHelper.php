<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Model\UserMetadataModel;

/**
 * Board Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class BoardHelper extends Base
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
        return $this->userMetadataCacheDecorator->get(UserMetadataModel::KEY_BOARD_COLLAPSED.$project_id, 0) == 1;
    }
}
