<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\ProjectActionDuplicationController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\TaskModel;
use KanboardTests\units\Base;

class ProjectActionDuplicationControllerTest extends Base
{
    public function testSaveSkipsActionsWithDestinationProjectWhereUserCannotCreateTasks()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'user')));

        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Source')));
        $this->assertEquals(2, $this->container['projectModel']->create(array('name' => 'Target')));
        $this->assertEquals(3, $this->container['projectModel']->create(array('name' => 'Read only')));
        $this->assertEquals(4, $this->container['projectModel']->create(array('name' => 'Writable')));

        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_VIEWER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(2, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(3, 2, Role::PROJECT_VIEWER));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(4, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $this->container['actionModel']->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CLOSE,
            'action_name' => '\Kanboard\Action\TaskMoveAnotherProject',
            'params' => array('column_id' => 1, 'project_id' => 3),
        )));

        $this->assertEquals(2, $this->container['actionModel']->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CLOSE,
            'action_name' => '\Kanboard\Action\TaskMoveAnotherProject',
            'params' => array('column_id' => 1, 'project_id' => 4),
        )));

        $this->container['userSession']->initialize($this->container['userModel']->getById(2));

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('project_id' => 2),
            array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'src_project_id' => 1,
            ),
            array(),
            array()
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        $controller = new ProjectActionDuplicationController($this->container);
        $controller->save();

        $actions = $this->container['actionModel']->getAllByProject(2);
        $this->assertCount(1, $actions);
        $this->assertEquals(array('column_id' => 5, 'project_id' => 4), $actions[0]['params']);
    }
}
