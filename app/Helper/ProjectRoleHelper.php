<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectRoleRestrictionModel;

/**
 * Class ProjectRoleHelper
 *
 * @package Kanboard\Helper
 * @author  Frederic Guillot
 */
class ProjectRoleHelper extends Base
{
    /**
     * Get project role for the current user
     *
     * @access public
     * @param  integer $project_id
     * @return string
     */
    public function getProjectUserRole($project_id)
    {
        return $this->memoryCache->proxy($this->projectUserRoleModel, 'getUserRole', $project_id, $this->userSession->getId());
    }

    /**
     * Return true if the task can be moved by the logged user
     *
     * @param array $task
     * @return bool
     */
    public function isDraggable(array &$task)
    {
        if ($task['is_active'] == 1 && $this->helper->user->hasProjectAccess('BoardViewController', 'save', $task['project_id'])) {
            return $this->isSortableColumn($task['project_id'], $task['column_id']);
        }

        return false;
    }

    /**
     * Return true is the column is sortable
     *
     * @param  int $project_id
     * @param  int $column_id
     * @return bool
     */
    public function isSortableColumn($project_id, $column_id)
    {
        $role = $this->getProjectUserRole($project_id);

        if ($this->role->isCustomProjectRole($role)) {
            $sortableColumns = $this->columnMoveRestrictionCacheDecorator->getSortableColumns($project_id, $role);

            foreach ($sortableColumns as $column) {
                if ($column['src_column_id'] == $column_id || $column['dst_column_id'] == $column_id) {
                    return true;
                }
            }

            return empty($sortableColumns);
        }

        return true;
    }

    /**
     * Check if the user can move a task
     *
     * @param  int $project_id
     * @param  int $src_column_id
     * @param  int $dst_column_id
     * @return bool|int
     */
    public function canMoveTask($project_id, $src_column_id, $dst_column_id)
    {
        $role = $this->getProjectUserRole($project_id);

        if ($this->role->isCustomProjectRole($role)) {
            if ($src_column_id == $dst_column_id) {
                return true;
            }

            $sortableColumns = $this->columnMoveRestrictionCacheDecorator->getSortableColumns($project_id, $role);

            foreach ($sortableColumns as $column) {
                if ($column['src_column_id'] == $src_column_id && $column['dst_column_id'] == $dst_column_id) {
                    return true;
                }

                if ($column['dst_column_id'] == $src_column_id && $column['src_column_id'] == $dst_column_id) {
                    return true;
                }
            }

            return empty($sortableColumns);
        }

        return true;
    }

    /**
     * Return true if the user can create a task for the given column
     *
     * @param  int $project_id
     * @param  int $column_id
     * @return bool
     */
    public function canCreateTaskInColumn($project_id, $column_id)
    {
        $role = $this->getProjectUserRole($project_id);

        if ($this->role->isCustomProjectRole($role)) {
            if (! $this->isAllowedToCreateTask($project_id, $column_id, $role)) {
                return false;
            }
        }

        return $this->helper->user->hasProjectAccess('TaskCreationController', 'show', $project_id);
    }

    /**
     * Return true if the user can create a task for the given column
     *
     * @param  int $project_id
     * @param  int $column_id
     * @return bool
     */
    public function canChangeTaskStatusInColumn($project_id, $column_id)
    {
        $role = $this->getProjectUserRole($project_id);

        if ($this->role->isCustomProjectRole($role)) {
            if (! $this->isAllowedToChangeTaskStatus($project_id, $column_id, $role)) {
                return false;
            }
        }

        return $this->helper->user->hasProjectAccess('TaskStatusController', 'close', $project_id);
    }

    /**
     * Return true if the user can remove a task
     *
     * Regular users can't remove tasks from other people
     *
     * @public
     * @param  array $task
     * @return bool
     */
    public function canRemoveTask(array $task)
    {
        if (isset($task['creator_id']) && $task['creator_id'] == $this->userSession->getId()) {
            return true;
        }

        if ($this->userSession->isAdmin() || $this->getProjectUserRole($task['project_id']) === Role::PROJECT_MANAGER) {
            return true;
        }

        return false;
    }

    /**
     * Check project access
     *
     * @param  string  $controller
     * @param  string  $action
     * @param  integer $project_id
     * @return bool
     */
    public function checkProjectAccess($controller, $action, $project_id)
    {
        if (! $this->userSession->isLogged()) {
            return false;
        }

        if ($this->userSession->isAdmin()) {
            return true;
        }

        if (! $this->helper->user->hasAccess($controller, $action)) {
            return false;
        }

        $role = $this->getProjectUserRole($project_id);

        if ($this->role->isCustomProjectRole($role)) {
            $result = $this->projectAuthorization->isAllowed($controller, $action, Role::PROJECT_MEMBER);
        } else {
            $result = $this->projectAuthorization->isAllowed($controller, $action, $role);
        }

        return $result;
    }

    /**
     * Check authorization for a custom project role to change the task status
     *
     * @param  int    $project_id
     * @param  int    $column_id
     * @param  string $role
     * @return bool
     */
    protected function isAllowedToChangeTaskStatus($project_id, $column_id, $role)
    {
        $columnRestrictions = $this->columnRestrictionCacheDecorator->getAllByRole($project_id, $role);

        foreach ($columnRestrictions as $restriction) {
            if ($restriction['column_id'] == $column_id) {
                if ($restriction['rule'] == ColumnRestrictionModel::RULE_ALLOW_TASK_OPEN_CLOSE) {
                    return true;
                } else if ($restriction['rule'] == ColumnRestrictionModel::RULE_BLOCK_TASK_OPEN_CLOSE) {
                    return false;
                }
            }
        }

        $projectRestrictions = $this->projectRoleRestrictionCacheDecorator->getAllByRole($project_id, $role);

        foreach ($projectRestrictions as $restriction) {
            if ($restriction['rule'] == ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check authorization for a custom project role to create a task
     *
     * @param  int    $project_id
     * @param  int    $column_id
     * @param  string $role
     * @return bool
     */
    protected function isAllowedToCreateTask($project_id, $column_id, $role)
    {
        $columnRestrictions = $this->columnRestrictionCacheDecorator->getAllByRole($project_id, $role);

        foreach ($columnRestrictions as $restriction) {
            if ($restriction['column_id'] == $column_id) {
                if ($restriction['rule'] == ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION) {
                    return true;
                } else if ($restriction['rule'] == ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION) {
                    return false;
                }
            }
        }

        $projectRestrictions = $this->projectRoleRestrictionCacheDecorator->getAllByRole($project_id, $role);

        foreach ($projectRestrictions as $restriction) {
            if ($restriction['rule'] == ProjectRoleRestrictionModel::RULE_TASK_CREATION) {
                return false;
            }
        }

        return true;
    }
}
