<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\TaskRecurrenceController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskRecurrenceControllerTest extends Base
{
    public function testUpdateOnlyChangesRecurrenceFields()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'member')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'Task A1')));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'member',
        );

        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array('task_id' => 1),
            array(
                'csrf_token'        => $this->container['token']->getCSRFToken(),
                'id'                => 1,
                'project_id'        => 2,
                'title'             => 'Changed',
                'column_id'         => 6,
                'is_active'         => 0,
                'recurrence_status' => TaskModel::RECURRING_STATUS_PENDING,
                'recurrence_factor' => 2,
            )
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        $controller = new TaskRecurrenceController($this->container);
        $controller->update();

        $task = $this->container['taskFinderModel']->getById(1);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals('Task A1', $task['title']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PENDING, $task['recurrence_status']);
        $this->assertEquals(2, $task['recurrence_factor']);
    }
}
