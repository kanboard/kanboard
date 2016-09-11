<?php

use Kanboard\Core\Security\Role;
use Kanboard\Helper\BoardHelper;
use Kanboard\Model\ColumnMoveRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskStatusModel;
use Kanboard\Model\UserModel;

require_once __DIR__.'/../Base.php';

class BoardHelperTest extends Base
{
    public function testIsDraggableWithProjectMember()
    {
        $boardHelper = new BoardHelper($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $task = $taskFinderModel->getById(1);
        $this->assertTrue($boardHelper->isDraggable($task));
    }

    public function testIsDraggableWithClosedTask()
    {
        $boardHelper = new BoardHelper($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskStatusModel = new TaskStatusModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertTrue($projectUserRole->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertTrue($taskStatusModel->close(1));

        $task = $taskFinderModel->getById(1);
        $this->assertFalse($boardHelper->isDraggable($task));
    }

    public function testIsDraggableWithColumnRestrictions()
    {
        $boardHelper = new BoardHelper($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectUserRole = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $columnMoveRestrictionModel = new ColumnMoveRestrictionModel($this->container);

        $this->container['sessionStorage']->user = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));

        $this->assertEquals(1, $projectRoleModel->create(1, 'Custom Role'));
        $this->assertEquals(1, $columnMoveRestrictionModel->create(1, 1, 2, 3));

        $this->assertTrue($projectUserRole->addUser(1, 2, 'Custom Role'));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 3)));

        $task = $taskFinderModel->getById(1);
        $this->assertTrue($boardHelper->isDraggable($task));

        $task = $taskFinderModel->getById(2);
        $this->assertFalse($boardHelper->isDraggable($task));
    }
}
