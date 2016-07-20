<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\TaskEvent;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Action\TaskDuplicateAnotherProject;

class TaskDuplicateAnotherProjectTest extends Base
{
    public function testSuccess()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 2,
            )
        ));

        $action = new TaskDuplicateAnotherProject($this->container);
        $action->setProjectId(1);
        $action->setParam('project_id', 2);
        $action->setParam('column_id', 2);

        $this->assertTrue($action->execute($event, TaskModel::EVENT_CLOSE));

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(2, $task['project_id']);
    }

    public function testWithWrongColumn()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        $event = new TaskEvent(array(
            'task_id' => 1,
            'task' => array(
                'project_id' => 1,
                'column_id' => 3,
            )
        ));

        $action = new TaskDuplicateAnotherProject($this->container);
        $action->setProjectId(1);
        $action->setParam('project_id', 2);
        $action->setParam('column_id', 2);

        $this->assertFalse($action->execute($event, TaskModel::EVENT_CLOSE));
    }
}
