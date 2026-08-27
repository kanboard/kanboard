<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\FileViewerController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\ObjectStorage\FileStorage;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectFileModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class FileViewerControllerTest extends Base
{
    public function testDownloadTaskFileWithTheProjectIdOfAnotherProject()
    {
        $this->createFixtures();
        $this->buildRequest(array('project_id' => 1, 'task_id' => 1, 'file_id' => 1));
        $this->expectNoDownload();

        $controller = new FileViewerController($this->container);

        $this->expectException('Kanboard\Core\Controller\PageNotFoundException');
        $controller->download();
    }

    public function testDownloadTaskFileWithTheProjectIdOfTheTask()
    {
        $this->createFixtures();
        $this->buildRequest(array('project_id' => 2, 'task_id' => 1, 'file_id' => 1));
        $this->expectDownload();

        $controller = new FileViewerController($this->container);
        $controller->download();
    }

    public function testDownloadTaskFileWithoutProjectId()
    {
        $this->createFixtures();
        $this->buildRequest(array('task_id' => 1, 'file_id' => 1));
        $this->expectDownload();

        $controller = new FileViewerController($this->container);
        $controller->download();
    }

    public function testDownloadProjectFileWithoutProjectId()
    {
        $this->createFixtures();

        $projectFileModel = new ProjectFileModel($this->container);
        $this->assertEquals(1, $projectFileModel->create(2, 'contract.txt', 'projects/2/contract.txt', 42));

        $this->buildRequest(array('file_id' => 1));
        $this->expectNoDownload();

        $controller = new FileViewerController($this->container);

        $this->expectException('Kanboard\Core\Controller\PageNotFoundException');
        $controller->download();
    }

    private function expectDownload()
    {
        $this->container['response']
            ->expects($this->once())
            ->method('withFileDownload')
            ->with('secret.txt');

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('output')
            ->with('tasks/1/secret.txt');
    }

    private function expectNoDownload()
    {
        $this->container['response']
            ->expects($this->never())
            ->method('withFileDownload');

        $this->container['objectStorage']
            ->expects($this->never())
            ->method('output');
    }

    private function buildRequest(array $params)
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), $params);

        $this->container['response'] = $this
            ->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('withFileDownload', 'send'))
            ->getMock();

        $this->container['objectStorage'] = $this
            ->getMockBuilder(FileStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array('output'))
            ->getMock();
    }

    private function createFixtures()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFileModel = new TaskFileModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'bob')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 2, 'title' => 'Task B')));
        $this->assertEquals(1, $taskFileModel->create(1, 'secret.txt', 'tasks/1/secret.txt', 42));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'bob',
        );
    }
}
