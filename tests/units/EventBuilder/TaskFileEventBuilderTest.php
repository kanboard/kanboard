<?php

use Kanboard\EventBuilder\TaskFileEventBuilder;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFileModel;

require_once __DIR__.'/../Base.php';

class TaskFileEventBuilderTest extends Base
{
    public function testWithMissingFile()
    {
        $taskFileEventBuilder = new TaskFileEventBuilder($this->container);
        $taskFileEventBuilder->withFileId(42);
        $this->assertNull($taskFileEventBuilder->buildEvent());
    }

    public function testBuild()
    {
        $taskFileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskFileEventBuilder = new TaskFileEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $taskFileModel->create(1, 'Test', '/tmp/test', 123));

        $event = $taskFileEventBuilder->withFileId(1)->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\TaskFileEvent', $event);
        $this->assertNotEmpty($event['file']);
        $this->assertNotEmpty($event['task']);
        $this->assertEquals(1, $event->getTaskId());
        $this->assertEquals(1, $event->getProjectId());
    }
}
