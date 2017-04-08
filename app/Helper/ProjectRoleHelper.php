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
     * @param  integer $projectId
     * @return string
     */
    public function getProjectUserRole($projectId)
    {
        return $this->memoryCache->proxy($this->projectUserRoleModel, 'getUserRole', $projectId, $this->userSession->getId());
    }

    /**
     * Return true if the task can be moved by the logged user
     *
     * @param array $task
     * @return bool
     */
    public function isDraggable(array &$task)
    {
        if ($task['is_active'] == 1 && $this->helper->user->hasProjectAccess('BoardAjaxController', 'save', $task['project_id'])) {
            return $this->isSortableColumn($task['project_id'], $task['column_id'], $task['owner_id']);
        }

        return false;
    }

    /**
     * Return true is the column is sortable
     *
     * @param  int $projectId
     * @param  int $columnId
     * @param  int $assigneeId
     * @return bool
     */
    public function isSortableColumn($projectId, $columnId, $assigneeId = null)
    {
        $role = $this->getProjectUserRole($projectId);

        if ($this->role->isCustomProjectRole($role)) {
            $sortableColumns = $this->columnMoveRestrictionCacheDecorator->getSortableColumns($projectId, $role);

            foreach ($sortableColumns as $column) {
                if ($column['src_column_id'] == $columnId || $column['dst_column_id'] == $columnId) {
                    if ($column['only_assigned'] == 1 && $assigneeId !== null && $assigneeId != $this->userSession->getId()) {
                        return false;
                    }

                    return true;
                }
            }

            return empty($sortableColumns) && $this->isAllowedToMoveTask($projectId, $role);
        }

        return true;
    }

    /**
     * Check if the user can move a task
     *
     * @param  int $projectId
     * @param  int $srcColumnId
     * @param  int $dstColumnId
     * @return bool|int
     */
    public function canMoveTask($projectId, $srcColumnId, $dstColumnId)
    {
        $role = $this->getProjectUserRole($projectId);

        if ($this->role->isCustomProjectRole($role)) {
            if ($srcColumnId == $dstColumnId) {
                return true;
            }

            $sortableColumns = $this->columnMoveRestrictionCacheDecorator->getSortableColumns($projectId, $role);

            foreach ($sortableColumns as $column) {
                if ($column['src_column_id'] == $srcColumnId && $column['dst_column_id'] == $dstColumnId) {
                    return true;
                }

                if ($column['dst_column_id'] == $srcColumnId && $column['src_column_id'] == $dstColumnId) {
                    return true;
                }
            }

            return empty($sortableColumns) && $this->isAllowedToMoveTask($projectId, $role);
        }

        return true;
    }

    /**
     * Return true if the user can create a task for the given column
     *
     * @param  int $projectId
     * @param  int $columnId
     * @return bool
     */
    public function canCreateTaskInColumn($projectId, $columnId)
    {
        $role = $this->getProjectUserRole($projectId);

        if ($this->role->isCustomProjectRole($role)) {
            if (! $this->isAllowedToCreateTask($projectId, $columnId, $role)) {
                return false;
            }
        }

        return $this->helper->user->hasProjectAccess('TaskCreationController', 'show', $projectId);
    }

    /**
     * Return true if the user can create a task for the given column
     *
     * @param  int $projectId
     * @param  int $columnId
     * @return bool
     */
    public function canChangeTaskStatusInColumn($projectId, $columnId)
    {
        $role = $this->getProjectUserRole($projectId);

        if ($this->role->isCustomProjectRole($role)) {
            if (! $this->isAllowedToChangeTaskStatus($projectId, $columnId, $role)) {
                return false;
            }
        }

        return $this->helper->user->hasProjectAccess('TaskStatusController', 'close', $projectId);
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
        $role = $this->getProjectUserRole($task['project_id']);

        if ($this->hasRestriction($task['project_id'], $role, ProjectRoleRestrictionModel::RULE_TASK_SUPPRESSION)) {
            return false;
        }

        if (isset($task['creator_id']) && $task['creator_id'] == $this->userSession->getId()) {
            return true;
        }

        if ($this->userSession->isAdmin() || $this->getProjectUserRole($task['project_id']) === Role::PROJECT_MANAGER) {
            return true;
        }

        return false;
    }

    /**
     * Return true if the user can change assignee
     *
     * @public
     * @param  array $task
     * @return bool
     */
    public function canChangeAssignee(array $task)
    {
        $role = $this->getProjectUserRole($task['project_id']);

        if ($this->role->isCustomProjectRole($role) && $this->hasRestriction($task['project_id'], $role, ProjectRoleRestrictionModel::RULE_TASK_CHANGE_ASSIGNEE)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if the user can update a task
     *
     * @public
     * @param  array $task
     * @return bool
     */
    public function canUpdateTask(array $task)
    {
        $role = $this->getProjectUserRole($task['project_id']);

        if ($this->role->isCustomProjectRole($role) && $task['owner_id'] != $this->userSession->getId() && $this->hasRestriction($task['project_id'], $role, ProjectRoleRestrictionModel::RULE_TASK_UPDATE_ASSIGNED)) {
            return false;
        }

        return true;
    }

    /**
     * Check project access
     *
     * @param  string  $controller
     * @param  string  $action
     * @param  integer $projectId
     * @return bool
     */
    public function checkProjectAccess($controller, $action, $projectId)
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

        $role = $this->getProjectUserRole($projectId);

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
     * @param  int    $projectId
     * @param  int    $columnId
     * @param  string $role
     * @return bool
     */
    protected function isAllowedToChangeTaskStatus($projectId, $columnId, $role)
    {
        $columnRestrictions = $this->columnRestrictionCacheDecorator->getAllByRole($projectId, $role);

        foreach ($columnRestrictions as $restriction) {
            if ($restriction['column_id'] == $columnId) {
                if ($restriction['rule'] == ColumnRestrictionModel::RULE_ALLOW_TASK_OPEN_CLOSE) {
                    return true;
                } else if ($restriction['rule'] == ColumnRestrictionModel::RULE_BLOCK_TASK_OPEN_CLOSE) {
                    return false;
                }
            }
        }

        return ! $this->hasRestriction($projectId, $role, ProjectRoleRestrictionModel::RULE_TASK_OPEN_CLOSE);
    }

    /**
     * Check authorization for a custom project role to create a task
     *
     * @param  int    $projectId
     * @param  int    $columnId
     * @param  string $role
     * @return bool
     */
    protected function isAllowedToCreateTask($projectId, $columnId, $role)
    {
        $columnRestrictions = $this->columnRestrictionCacheDecorator->getAllByRole($projectId, $role);

        foreach ($columnRestrictions as $restriction) {
            if ($restriction['column_id'] == $columnId) {
                if ($restriction['rule'] == ColumnRestrictionModel::RULE_ALLOW_TASK_CREATION) {
                    return true;
                } else if ($restriction['rule'] == ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION) {
                    return false;
                }
            }
        }

        return ! $this->hasRestriction($projectId, $role, ProjectRoleRestrictionModel::RULE_TASK_CREATION);
    }

    /**
     * Check if the role can move task in the given project
     *
     * @param  int     $projectId
     * @param  string  $role
     * @return bool
     */
    protected function isAllowedToMoveTask($projectId, $role)
    {
        $projectRestrictions = $this->projectRoleRestrictionCacheDecorator->getAllByRole($projectId, $role);

        foreach ($projectRestrictions as $restriction) {
            if ($restriction['rule'] == ProjectRoleRestrictionModel::RULE_TASK_MOVE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if given role has a restriction
     *
     * @param  integer $projectId
     * @param  string  $role
     * @param  string  $rule
     * @return bool
     */
    protected function hasRestriction($projectId, $role, $rule)
    {
        $projectRestrictions = $this->projectRoleRestrictionCacheDecorator->getAllByRole($projectId, $role);

        foreach ($projectRestrictions as $restriction) {
            if ($restriction['rule'] == $rule) {
                return true;
            }
        }

        return false;
    }
}
