<?php

namespace KanboardTests\units\Action;

use Kanboard\Action\SubtaskTimerMoveTaskColumn;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use KanboardTests\units\Base;

class SubtaskTimerMoveTaskColumnTest extends Base
{
    public function testCreateSubtaskWithoutChangingTaskEstimate()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array(
            'project_id' => 1,
            'title' => 'task',
            'time_estimated' => 5,
        )));

        $task = $taskFinderModel->getById(1);
        $action = new SubtaskTimerMoveTaskColumn($this->container);
        $action->setParam('subtask', 'automatic subtask');

        $action->doAction(array('task' => $task));

        $task = $taskFinderModel->getById(1);
        $subtask = $subtaskModel->getById(1);

        $this->assertEquals(5, $task['time_estimated']);
        $this->assertEquals('automatic subtask', $subtask['title']);
        $this->assertEquals(0, $subtask['time_estimated']);
    }
}
