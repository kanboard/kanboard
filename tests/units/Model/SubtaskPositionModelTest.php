<?php

use Kanboard\Model\SubtaskPositionModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\ProjectModel;

require_once __DIR__.'/../Base.php';

class SubtaskPositionModelTest extends Base
{
    public function testChangePosition()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskPositionModel = new SubtaskPositionModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('title' => 'subtask #3', 'task_id' => 1)));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(2, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskPositionModel->changePosition(1, 3, 2));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(3, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(2, $subtasks[2]['id']);

        $this->assertTrue($subtaskPositionModel->changePosition(1, 2, 1));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(1, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskPositionModel->changePosition(1, 2, 2));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(1, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(2, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(3, $subtasks[2]['id']);

        $this->assertTrue($subtaskPositionModel->changePosition(1, 1, 3));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[0]['id']);
        $this->assertEquals(2, $subtasks[1]['position']);
        $this->assertEquals(3, $subtasks[1]['id']);
        $this->assertEquals(3, $subtasks[2]['position']);
        $this->assertEquals(1, $subtasks[2]['id']);

        $this->assertFalse($subtaskPositionModel->changePosition(1, 2, 0));
        $this->assertFalse($subtaskPositionModel->changePosition(1, 2, 4));
    }
}
