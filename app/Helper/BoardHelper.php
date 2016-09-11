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

    /**
     * Return true if the task can be moved by the connected user
     *
     * @param array $task
     * @return bool
     */
    public function isDraggable(array $task)
    {
        if ($task['is_active'] == 1 && $this->helper->user->hasProjectAccess('BoardViewController', 'save', $task['project_id'])) {
            $role = $this->helper->user->getProjectUserRole($task['project_id']);

            if ($this->role->isCustomProjectRole($role)) {
                $srcColumnIds = $this->columnMoveRestrictionCacheDecorator->getAllSrcColumns($task['project_id'], $role);
                return ! isset($srcColumnIds[$task['column_id']]);
            }

            return true;
        }

        return false;
    }
}
