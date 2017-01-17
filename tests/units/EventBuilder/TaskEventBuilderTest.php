<?php

use Kanboard\EventBuilder\TaskEventBuilder;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class TaskEventBuilderTest extends Base
{
    public function testWithMissingTask()
    {
        $taskEventBuilder = new TaskEventBuilder($this->container);
        $taskEventBuilder->withTaskId(42);
        $this->assertNull($taskEventBuilder->buildEvent());
    }

    public function testBuildWithTask()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskEventBuilder = new TaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'before', 'project_id' => 1)));

        $event = $taskEventBuilder
            ->withTaskId(1)
            ->withTask(array('title' => 'before'))
            ->withChanges(array('title' => 'after'))
            ->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertNotEmpty($event['task']);
        $this->assertEquals(1, $event['task_id']);
        $this->assertEquals(1, $event->getTaskId());
        $this->assertEquals(1, $event->getProjectId());
        $this->assertEquals(array('title' => 'after'), $event['changes']);
    }

    public function testBuildWithoutChanges()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskEventBuilder = new TaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $event = $taskEventBuilder->withTaskId(1)->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertNotEmpty($event['task']);
        $this->assertEquals(1, $event['task_id']);
        $this->assertArrayNotHasKey('changes', $event);
    }

    public function testBuildWithChanges()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskEventBuilder = new TaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $event = $taskEventBuilder
            ->withTaskId(1)
            ->withChanges(array('title' => 'new title'))
            ->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertNotEmpty($event['task']);
        $this->assertNotEmpty($event['changes']);
        $this->assertEquals('new title', $event['changes']['title']);
    }

    public function testBuildWithChangesAndValues()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskEventBuilder = new TaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $event = $taskEventBuilder
            ->withTaskId(1)
            ->withChanges(array('title' => 'new title', 'project_id' => 1))
            ->withValues(array('key' => 'value'))
            ->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);
        $this->assertNotEmpty($event['task']);
        $this->assertNotEmpty($event['changes']);
        $this->assertNotEmpty($event['key']);
        $this->assertEquals('value', $event['key']);

        $this->assertCount(1, $event['changes']);
        $this->assertEquals('new title', $event['changes']['title']);
    }
}
