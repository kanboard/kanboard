<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

class TaskBulkChangePropertyController extends BaseController
{
    public function show(array $values = [], array $errors = [])
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['task_ids'] = $this->request->getStringParam('task_ids');
        }

        $this->response->html($this->template->render('task_bulk_change_property/show', [
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id']),
            'categories_list' => $this->categoryModel->getList($project['id']),
        ]));
    }

    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $taskIDs = explode(',', $values['task_ids']);

        foreach ($taskIDs as $taskID) {
            $changes = [];

            if (isset($values['change_color']) && $values['change_color'] == 1) {
                $changes['color_id'] = $values['color_id'];
            }

            if (isset($values['change_assignee']) && $values['change_assignee'] == 1) {
                $changes['owner_id'] = $values['owner_id'];
            }

            if (isset($values['change_priority']) && $values['change_priority'] == 1) {
                $changes['priority'] = $values['priority'];
            }

            if (isset($values['change_category']) && $values['change_category'] == 1) {
                $changes['category_id'] = $values['category_id'];
            }

            if (isset($values['change_tags']) && $values['change_tags'] == 1) {
                $changes['tags'] = $values['tags'];
            }

            if (isset($values['change_due_date']) && $values['change_due_date'] == 1) {
                $changes['date_due'] = $values['date_due'];
            }

            if (isset($values['change_start_date']) && $values['change_start_date'] == 1) {
                $changes['date_started'] = $values['date_started'];
            }

            if (isset($values['change_estimated_time']) && $values['change_estimated_time'] == 1) {
                $changes['time_estimated'] = $values['time_estimated'];
            }

            if (isset($values['change_spent_time']) && $values['change_spent_time'] == 1) {
                $changes['time_spent'] = $values['time_spent'];
            }

            if (isset($values['change_score']) && $values['change_score'] == 1) {
                $changes['score'] = $values['score'];
            }

            if (! empty($changes)) {
                $changes['id'] = $taskID;
                $this->taskModificationModel->update($changes);
            }
        }

        $this->response->redirect($this->helper->url->to('TaskListController', 'show', ['project_id' => $project['id']]), true);
    }
}
