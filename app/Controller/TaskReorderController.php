<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

class TaskReorderController extends BaseController
{
    public function reorderColumn()
    {
        $project = $this->getProject();

        if (! $this->helper->user->hasProjectAccess('TaskModificationController', 'update', $project['id'])) {
            throw new AccessForbiddenException();
        }

        $swimlaneID = $this->request->getIntegerParam('swimlane_id');
        $columnID = $this->request->getIntegerParam('column_id');
        $direction = $this->request->getStringParam('direction');
        $sort = $this->request->getStringParam('sort');

        switch ($sort) {
            case 'id':
                $this->taskReorderModel->reorderByTaskId($project['id'], $swimlaneID, $columnID, $direction);
                break;
            case 'priority':
                $this->taskReorderModel->reorderByPriority($project['id'], $swimlaneID, $columnID, $direction);
                break;
            case 'assignee-priority':
                $this->taskReorderModel->reorderByAssigneeAndPriority($project['id'], $swimlaneID, $columnID, $direction);
                break;
            case 'assignee':
                $this->taskReorderModel->reorderByAssignee($project['id'], $swimlaneID, $columnID, $direction);
                break;
            case 'due-date':
                $this->taskReorderModel->reorderByDueDate($project['id'], $swimlaneID, $columnID, $direction);
                break;
        }

        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', ['project_id' => $project['id']]));
    }
}
