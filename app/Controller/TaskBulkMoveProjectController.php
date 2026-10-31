<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Bulk move tasks to another project
 *
 * @package Kanboard\Controller
 */
class TaskBulkMoveProjectController extends BaseController
{
    /**
     * Show the destination project form
     *
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['task_ids'] = $this->request->getStringParam('task_ids');
        }

        $projects_list = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        unset($projects_list[$project['id']]);

        if (! empty($projects_list)) {
            $dst_project_id = $this->request->getIntegerParam('dst_project_id', key($projects_list));

            if (! isset($projects_list[$dst_project_id])) {
                $dst_project_id = key($projects_list);
            }

            $swimlanes_list = $this->swimlaneModel->getList($dst_project_id, false, true);
            $columns_list = $this->columnModel->getList($dst_project_id);
            $categories_list = $this->categoryModel->getList($dst_project_id);
            $users_list = $this->projectUserRoleModel->getAssignableUsersList($dst_project_id);
        } else {
            $dst_project_id = 0;
            $swimlanes_list = array();
            $columns_list = array();
            $categories_list = array();
            $users_list = array();
        }

        $this->response->html($this->template->render('task_bulk_move_project/show', array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'projects_list' => $projects_list,
            'dst_project_id' => $dst_project_id,
            'swimlanes_list' => $swimlanes_list,
            'columns_list' => $columns_list,
            'categories_list' => $categories_list,
            'users_list' => $users_list,
        )));
    }

    /**
     * Move all selected tasks to the destination project
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $taskIDs = isset($values['task_ids']) && is_string($values['task_ids'])
            ? array_filter(array_unique(explode(',', $values['task_ids'])), 'is_numeric')
            : array();
        $dst_project_id = isset($values['project_id']) && is_string($values['project_id']) ? (int) $values['project_id'] : 0;

        $projects_list = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        unset($projects_list[$project['id']]);

        if (empty($taskIDs) || ! isset($projects_list[$dst_project_id])) {
            $this->flash->failure(t('Unable to move these tasks to another project.'));
            $this->response->redirect($this->helper->url->to('TaskListController', 'show', array('project_id' => $project['id'])));
            return;
        }

        if (! $this->projectPermissionModel->isUserAllowed($dst_project_id, $this->userSession->getId())) {
            throw new AccessForbiddenException();
        }

        // "Keep current value" is the form default: null lets each task inherit its own
        // swimlane/column/category/assignee, remapped by name to the destination project
        $swimlane_id = $this->getPositiveIdOrNull($values, 'swimlane_id');
        $column_id = $this->getPositiveIdOrNull($values, 'column_id');
        $category_id = $this->getIntOrNull($values, 'category_id');
        $owner_id = $this->getIntOrNull($values, 'owner_id');

        // Validate the whole batch first so a forbidden task id cannot leave a half-applied move
        foreach ($taskIDs as $taskID) {
            if ($this->taskFinderModel->getProjectId($taskID) != $project['id']) {
                throw new AccessForbiddenException();
            }
        }

        $count = 0;

        foreach ($taskIDs as $taskID) {
            $this->taskProjectMoveModel->moveToProject($taskID, $dst_project_id, $swimlane_id, $column_id, $category_id, $owner_id);
            $count++;
        }

        $this->flash->success(t('%d task(s) moved to another project.', $count));
        $this->response->redirect($this->helper->url->to('TaskListController', 'show', array('project_id' => $dst_project_id)));
    }

    /**
     * Normalize a destination select value: empty string means "keep the task's current value"
     *
     * @access private
     * @param  array  $values
     * @param  string $field
     * @return integer|null
     */
    private function getIntOrNull(array $values, $field)
    {
        $value = isset($values[$field]) && is_string($values[$field]) ? trim($values[$field]) : '';

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Same as getIntOrNull but a non-positive id is treated as "keep the current value" as well
     *
     * @access private
     * @param  array  $values
     * @param  string $field
     * @return integer|null
     */
    private function getPositiveIdOrNull(array $values, $field)
    {
        $value = $this->getIntOrNull($values, $field);

        return $value !== null && $value > 0 ? $value : null;
    }
}
