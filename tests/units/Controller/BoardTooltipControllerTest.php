<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\BoardTooltipController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\SwimlaneModel;
use KanboardTests\units\Base;

class BoardTooltipControllerTest extends Base
{
    public function testSwimlaneFromAnotherProjectIsForbidden()
    {
        $this->createProjects();
        $swimlaneModel = new SwimlaneModel($this->container);
        $foreignSwimlane = $swimlaneModel->getFirstActiveSwimlane(2);

        $this->buildRequest(array('project_id' => 1, 'swimlane_id' => $foreignSwimlane['id']));

        $this->expectException(AccessForbiddenException::class);
        (new BoardTooltipController($this->container))->swimlane();
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
