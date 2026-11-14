<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskViewController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskLinkModel;
use KanboardTests\units\Base;

class TaskViewControllerTest extends Base
{
    public function testPublicTaskDoesNotExposeLinksToOtherProjects()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Public project')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Private project')));
        $this->assertTrue($projectModel->enablePublicAccess(1));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Public task')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Private task')));
        $this->assertNotFalse($taskLinkModel->create(1, 2, 1));

        $project = $projectModel->getById(1);
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), array(
            'task_id' => 1,
            'token' => $project['token'],
        ));

        $html = null;
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();
        $this->container['response']
            ->expects($this->once())
            ->method('html')
            ->willReturnCallback(function ($data) use (&$html) {
                $html = $data;
            });

        (new TaskViewController($this->container))->readonly();

        $this->assertStringContainsString('Public task', $html);
        $this->assertStringNotContainsString('Private task', $html);
        $this->assertStringNotContainsString('Private project', $html);
    }
}
