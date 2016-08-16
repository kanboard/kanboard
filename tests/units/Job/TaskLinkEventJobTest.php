<?php

use Kanboard\Job\TaskLinkEventJob;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskLinkModel;

require_once __DIR__.'/../Base.php';

class TaskLinkEventJobTest extends Base
{
    public function testJobParams()
    {
        $taskLinkEventJob = new TaskLinkEventJob($this->container);
        $taskLinkEventJob->withParams(123, 'foobar');

        $this->assertSame(array(123, 'foobar'), $taskLinkEventJob->getJobParams());
    }

    public function testWithMissingLink()
    {
        $this->container['dispatcher']->addListener(TaskLinkModel::EVENT_CREATE_UPDATE, function() {});

        $taskLinkEventJob = new TaskLinkEventJob($this->container);
        $taskLinkEventJob->execute(42, TaskLinkModel::EVENT_CREATE_UPDATE);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerCreationEvents()
    {
        $this->container['dispatcher']->addListener(TaskLinkModel::EVENT_CREATE_UPDATE, function() {});

        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'task 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'task 2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskLinkModel::EVENT_CREATE_UPDATE.'.closure', $called);
    }

    public function testTriggerDeleteEvents()
    {
        $this->container['dispatcher']->addListener(TaskLinkModel::EVENT_DELETE, function() {});

        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'task 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'task 2', 'project_id' => 1)));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));
        $this->assertTrue($taskLinkModel->remove(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskLinkModel::EVENT_DELETE.'.closure', $called);
    }
}
