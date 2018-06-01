<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

class TaskBulkMoveColumnController extends BaseController
{
    public function show(array $values = [], array $errors = [])
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['task_ids'] = $this->request->getStringParam('task_ids');
        }

        $this->response->html($this->template->render('task_bulk_move_column/show', [
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'columns' => $this->columnModel->getList($project['id']),
            'swimlanes' => $this->swimlaneModel->getList($project['id'], false, true),
        ]));
    }

    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $taskIDs = explode(',', $values['task_ids']);

        foreach ($taskIDs as $taskID) {
            $task = $this->taskFinderModel->getById($taskID);

            if (! $this->helper->projectRole->canMoveTask($task['project_id'], $task['column_id'], $values['column_id'])) {
                throw new AccessForbiddenException(e('You are not allowed to move this task.'));
            }

            $this->taskPositionModel->moveBottom($project['id'], $taskID, $values['swimlane_id'], $values['column_id']);
        }

        $this->response->redirect($this->helper->url->to('TaskListController', 'show', ['project_id' => $project['id']]), true);
    }
}
