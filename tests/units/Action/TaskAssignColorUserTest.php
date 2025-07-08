<?php

namespace KanboardTests\units\Action;

use KanboardTests\units\Base;
use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskAssignColorUser;

class TaskAssignColorUserTest extends Base
{
    public function testChangeColor()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'owner_id' => 1,
            )
        ));

        $action = new TaskAssignColorUser($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('user_id', 1);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_ASSIGNEE_CHANGE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('red', $task['color_id']);
    }

    public function testWithWrongUser()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'owner_id' => 2,
            )
        ));

        $action = new TaskAssignColorUser($this->container);
        $action->setProjectId(1);
        $action->setParam('color_id', 'red');
        $action->setParam('user_id', 1);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_ASSIGNEE_CHANGE));
    }
}
