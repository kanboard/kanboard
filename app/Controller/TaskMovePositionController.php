<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Formatter\BoardFormatter;

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
            'board' => BoardFormatter::getInstance($this->container)
                ->withProjectId($task['project_id'])
                ->withQuery($this->taskFinderModel->getExtendedQuery())
                ->format()
        )));
    }

    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getJson();

        if (! $this->helper->projectRole->canMoveTask($task['project_id'], $task['column_id'], $values['column_id'])) {
            throw new AccessForbiddenException(e("You don't have the permission to move this task"));
        }

        $result = $this->taskPositionModel->movePosition(
            $task['project_id'],
            $task['id'],
            $values['column_id'],
            $values['position'],
            $values['swimlane_id']
        );

        $this->response->json(array('result' => $result));
    }
}
