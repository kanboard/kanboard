<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\ActionCreationController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Template;
use Kanboard\Model\TaskModel;
use KanboardTests\units\Base;

class ActionCreationControllerTest extends Base
{
    public function testSaveWithDestinationProjectNotAccessible()
    {
        $this->createProjects();
        $this->buildSaveRequest(3);

        $controller = new ActionCreationController($this->container);

        try {
            $controller->save();
            $this->fail('An action with an inaccessible destination project must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertEmpty($this->container['actionModel']->getAllByProject(1));
        }
    }

    public function testSaveWithDestinationProjectAsViewer()
    {
        $this->createProjects(Role::PROJECT_VIEWER);
        $this->buildSaveRequest(2);

        $controller = new ActionCreationController($this->container);

        try {
            $controller->save();
            $this->fail('An action with a destination project where the user cannot create tasks must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertEmpty($this->container['actionModel']->getAllByProject(1));
        }
    }

    public function testParamsListsOnlyDestinationProjectsAllowingTaskCreation()
    {
        $this->createProjects(Role::PROJECT_MANAGER);
        $this->assertEquals(4, $this->container['projectModel']->create(array('name' => 'Project D')));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(4, 2, Role::PROJECT_VIEWER));

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('project_id' => 1),
            array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'event_name' => TaskModel::EVENT_CREATE,
                'action_name' => '\Kanboard\Action\TaskDuplicateAnotherProject',
            ),
            array(),
            array()
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('html');

        $params = new \ArrayObject();
        $this->container['template'] = $this->createStub(Template::class);
        $this->container['template']
            ->method('render')
            ->willReturnCallback(function ($template, array $templateParams = array()) use ($params) {
                $params[$template] = $templateParams;
                return '';
            });

        $controller = new ActionCreationController($this->container);
        $controller->params();

        $this->assertSame(array(2 => 'Project B'), $params['action_creation/params']['projects_list']);
    }

    public function testSaveWithDestinationProjectAccessible()
    {
        $this->createProjects(Role::PROJECT_MANAGER);
        $this->buildSaveRequest(2);

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        $controller = new ActionCreationController($this->container);
        $controller->save();

        $actions = $this->container['actionModel']->getAllByProject(1);
        $this->assertCount(1, $actions);
        $this->assertEquals(array('column_id' => 1, 'project_id' => 2), $actions[0]['params']);
    }

    private function buildSaveRequest($destination_project_id)
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('project_id' => 1),
            array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'event_name' => TaskModel::EVENT_CREATE,
                'action_name' => '\Kanboard\Action\TaskDuplicateAnotherProject',
                'params' => array(
                    'column_id' => 1,
                    'project_id' => $destination_project_id,
                ),
            ),
            array(),
            array()
        );
    }

    private function createProjects($destination_role = Role::PROJECT_MANAGER)
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'user1')));

        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Project A')));
        $this->assertEquals(2, $this->container['projectModel']->create(array('name' => 'Project B')));
        $this->assertEquals(3, $this->container['projectModel']->create(array('name' => 'Project C')));

        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(2, 2, $destination_role));

        $this->container['userSession']->initialize($this->container['userModel']->getById(2));
    }
}
