<?php

use Kanboard\Job\SubtaskEventJob;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class SubtaskEventJobTest extends Base
{
    public function testJobParams()
    {
        $subtaskEventJob = new SubtaskEventJob($this->container);
        $subtaskEventJob->withParams(123, array('foobar'), array('k' => 'v'));

        $this->assertSame(array(123, array('foobar'), array('k' => 'v')), $subtaskEventJob->getJobParams());
    }

    public function testWithMissingSubtask()
    {
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE, function() {});

        $subtaskEventJob = new SubtaskEventJob($this->container);
        $subtaskEventJob->execute(42, array(SubtaskModel::EVENT_CREATE));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerEvents()
    {
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE, function() {});
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_UPDATE, function() {});
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_DELETE, function() {});
        $this->container['dispatcher']->addListener(SubtaskModel::EVENT_CREATE_UPDATE, function() {});

        $subtaskModel = new SubtaskModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'before')));
        $this->assertTrue($subtaskModel->update(array('id' => 1, 'task_id' => 1, 'title' => 'after')));
        $this->assertTrue($subtaskModel->remove(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(SubtaskModel::EVENT_CREATE.'.closure', $called);
        $this->assertArrayHasKey(SubtaskModel::EVENT_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(SubtaskModel::EVENT_DELETE.'.closure', $called);
        $this->assertArrayHasKey(SubtaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
    }
}
