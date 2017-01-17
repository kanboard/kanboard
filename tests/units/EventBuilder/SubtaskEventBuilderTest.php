<?php

use Kanboard\EventBuilder\SubtaskEventBuilder;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class SubtaskEventBuilderTest extends Base
{
    public function testWithMissingSubtask()
    {
        $subtaskEventBuilder = new SubtaskEventBuilder($this->container);
        $subtaskEventBuilder->withSubtaskId(42);
        $this->assertNull($subtaskEventBuilder->buildEvent());
    }

    public function testBuildWithoutChanges()
    {
        $subtaskModel = new SubtaskModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskEventBuilder = new SubtaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'test')));

        $event = $subtaskEventBuilder->withSubtaskId(1)->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $this->assertNotEmpty($event['subtask']);
        $this->assertNotEmpty($event['task']);
        $this->assertArrayNotHasKey('changes', $event);
    }

    public function testBuildWithChanges()
    {
        $subtaskModel = new SubtaskModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskEventBuilder = new SubtaskEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'test')));

        $event = $subtaskEventBuilder
            ->withSubtaskId(1)
            ->withValues(array('title' => 'new title', 'user_id' => 1))
            ->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $this->assertNotEmpty($event['subtask']);
        $this->assertNotEmpty($event['task']);
        $this->assertNotEmpty($event['changes']);
        $this->assertCount(2, $event['changes']);
        $this->assertEquals('new title', $event['changes']['title']);
        $this->assertEquals(1, $event['changes']['user_id']);
        $this->assertEquals(1, $event->getTaskId());
        $this->assertEquals(1, $event->getProjectId());
    }
}
