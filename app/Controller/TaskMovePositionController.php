<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Model\TaskModel;

/**
 * Class TaskMovePositionController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TaskMovePositionController extends BaseController
{
    public function show()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task_move_position/show', array(
            'task' => $task,
            'board' => $this->boardFormatter
                ->withProjectId($task['project_id'])
                ->withQuery($this->taskFinderModel->getExtendedQuery()
                    ->eq(TaskModel::TABLE.'.is_active', TaskModel::STATUS_OPEN)
                    ->neq(TaskModel::TABLE.'.id', $task['id'])
                )
                ->format()
        )));
    }

    public function save()
    {
        $this->checkReusableGETCSRFParam();
        $task = $this->getTask();
        $values = $this->request->getJson();

        if (! $this->helper->projectRole->canMoveTask($task['project_id'], $task['column_id'], $values['column_id'])) {
            throw new AccessForbiddenException(e('You are not allowed to move this task.'));
        }

        $this->taskPositionModel->movePosition(
            $task['project_id'],
            $task['id'],
            $values['column_id'],
            $values['position'],
            $values['swimlane_id']
        );

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])));
    }
}
