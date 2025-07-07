<?php

namespace KanboardTests\units\Job;

use KanboardTests\units\Base;
use Kanboard\Job\ProjectFileEventJob;
use Kanboard\Model\ProjectFileModel;
use Kanboard\Model\ProjectModel;

class ProjectFileEventJobTest extends Base
{
    public function testJobParams()
    {
        $projectFileEventJob = new ProjectFileEventJob($this->container);
        $projectFileEventJob->withParams(123, 'foobar');

        $this->assertSame(array(123, 'foobar'), $projectFileEventJob->getJobParams());
    }

    public function testWithMissingFile()
    {
        $this->container['dispatcher']->addListener(ProjectFileModel::EVENT_CREATE, function () {});

        $projectFileEventJob = new ProjectFileEventJob($this->container);
        $projectFileEventJob->execute(42, ProjectFileModel::EVENT_CREATE);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerEvents()
    {
        $this->container['dispatcher']->addListener(ProjectFileModel::EVENT_CREATE, function () {});

        $projectModel = new ProjectModel($this->container);
        $projectFileModel = new ProjectFileModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $projectFileModel->create(1, 'Test', '/tmp/test', 123));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertCount(1, $called);
        $this->assertEquals(ProjectFileModel::EVENT_CREATE, $called[0]['event']);
    }
}
