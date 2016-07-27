<?php

use Kanboard\Job\TaskFileEventJob;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFileModel;

require_once __DIR__.'/../Base.php';

class TaskFileEventJobTest extends Base
{
    public function testJobParams()
    {
        $taskFileEventJob = new TaskFileEventJob($this->container);
        $taskFileEventJob->withParams(123, 'foobar');

        $this->assertSame(array(123, 'foobar'), $taskFileEventJob->getJobParams());
    }

    public function testWithMissingFile()
    {
        $this->container['dispatcher']->addListener(TaskFileModel::EVENT_CREATE, function() {});

        $taskFileEventJob = new TaskFileEventJob($this->container);
        $taskFileEventJob->execute(42, TaskFileModel::EVENT_CREATE);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerEvents()
    {
        $this->container['dispatcher']->addListener(TaskFileModel::EVENT_CREATE, function() {});

        $taskFileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $taskFileModel->create(1, 'Test', '/tmp/test', 123));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskFileModel::EVENT_CREATE.'.closure', $called);
    }
}
