<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Action\TaskAssignCurrentUser;

class TaskAssignCurrentUserTest extends Base
{
    public function testChangeUser()
    {
        $_SESSION['user'] = array('id' => 1);

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1));

        $action = new TaskAssignCurrentUser($this->container);
        $action->setProjectId(1);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CREATE));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function testWithNoUserSession()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1));

        $action = new TaskAssignCurrentUser($this->container);
        $action->setProjectId(1);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_CREATE));
    }
}
