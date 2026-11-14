<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\BoardPopoverController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use KanboardTests\units\Base;

class BoardPopoverControllerTest extends Base
{
    public function testConfirmCloseColumnTasksWithColumnFromAnotherProjectIsForbidden()
    {
        $this->createProjects();
        $columnModel = new ColumnModel($this->container);
        $foreignColumn = $columnModel->getAll(2)[0];
        $swimlaneModel = new SwimlaneModel($this->container);
        $swimlane = $swimlaneModel->getFirstActiveSwimlane(1);

        $this->buildRequest(array(
            'project_id' => 1,
            'column_id' => $foreignColumn['id'],
            'swimlane_id' => $swimlane['id'],
        ));

        $this->expectException(AccessForbiddenException::class);
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
    }

    public function testConfirmCloseColumnTasksWithSwimlaneFromAnotherProjectIsForbidden()
    {
        $this->createProjects();
        $columnModel = new ColumnModel($this->container);
        $column = $columnModel->getAll(1)[0];
        $swimlaneModel = new SwimlaneModel($this->container);
        $foreignSwimlane = $swimlaneModel->getFirstActiveSwimlane(2);

        $this->buildRequest(array(
            'project_id' => 1,
            'column_id' => $column['id'],
            'swimlane_id' => $foreignSwimlane['id'],
        ));

        $this->expectException(AccessForbiddenException::class);
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
    }

    private function buildRequest(array $params)
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), $params);
        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();

        $this->container['response']
            ->expects($this->never())
            ->method('html');
    }

    private function createProjects()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
    }
}
