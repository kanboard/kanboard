<?php

namespace KanboardTests\units\Validator;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ColumnRestrictionModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectRoleModel;
use Kanboard\Model\ProjectRoleRestrictionModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;
use Kanboard\Validator\ActionValidator;

class ActionValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $actionValidator = new ActionValidator($this->container);

        $values = array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CREATE,
            'action_name' => '\Kanboard\Action\TaskCloseColumn',
            'params' => array('column_id' => 1),
        );

        $result = $actionValidator->validateCreation($values);
        $this->assertTrue($result[0]);

        unset($values['params']);
        $result = $actionValidator->validateCreation($values);
        $this->assertFalse($result[0]);
    }

    public function testValidateParametersWithProjectResources()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createProjects();

        // Resources of the project that owns the action
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('column_id' => 1)));
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('src_column_id' => 1, 'dest_column_id' => 2)));
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('swimlane_id' => 1)));
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('category_id' => 1)));
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('user_id' => 1)));

        // Resources of another project
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => 5)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('src_column_id' => 1, 'dest_column_id' => 6)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('dest_swimlane_id' => 2)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('category_id' => 2)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('user_id' => 2)));

        // Values that do not exist at all
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => 0)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('swimlane_id' => 'foobar')));

        // "No category" and "Unassigned" are valid choices
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('category_id' => 0)));
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('user_id' => 0)));

        // Parameters that are not project resources
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('color_id' => 'red', 'duration' => 2, 'subject' => 'test')));
    }

    public function testValidateParametersWithMalformedValues()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createProjects();

        // Values sent as an array or an object must be rejected and not used as an array key
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => array())));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => array(1))));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('project_id' => array(1))));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('subject' => array('test'))));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => new \stdClass())));

        // Only integer ids are accepted for project resources
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('column_id' => '1')));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => 1.9)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => true)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => null)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => '01')));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => '')));
    }

    public function testValidateParametersWithDestinationProject()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createProjects();

        $_SESSION['user'] = array(
            'id' => 1,
            'role' => Role::APP_USER,
            'username' => 'user1',
        );

        // The user is member of both projects
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('column_id' => 1, 'project_id' => 2)));

        // The user is not member of the destination project
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('column_id' => 1, 'project_id' => 3)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('dst_project_id' => 3)));
        $this->assertFalse($actionValidator->validateParameters(1, 1, array('project_id' => 42)));

        // Application administrators can use any project
        $_SESSION['user']['role'] = Role::APP_ADMIN;
        $this->assertTrue($actionValidator->validateParameters(1, 1, array('project_id' => 3)));
    }

    public function testValidateParametersWithDestinationProjectAsMember()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createDestinationProject(Role::PROJECT_MEMBER);

        $this->assertTrue($actionValidator->validateParameters(1, 2, array('column_id' => 1, 'project_id' => 2)));
    }

    public function testValidateParametersWithDestinationProjectAsViewer()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createDestinationProject(Role::PROJECT_VIEWER);

        $this->assertFalse($actionValidator->validateParameters(1, 2, array('column_id' => 1, 'project_id' => 2)));
        $this->assertFalse($actionValidator->validateDestinationProjects(2, array('project_id' => 2)));
    }

    public function testValidateParametersWithDestinationProjectWithTaskCreationRestriction()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createDestinationProject('Custom Role');

        $projectRoleRestrictionModel = new ProjectRoleRestrictionModel($this->container);
        $this->assertEquals(1, $projectRoleRestrictionModel->create(2, 1, ProjectRoleRestrictionModel::RULE_TASK_CREATION));

        $this->assertFalse($actionValidator->validateParameters(1, 2, array('column_id' => 1, 'project_id' => 2)));
    }

    public function testValidateParametersWithDestinationProjectWithFirstColumnRestriction()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createDestinationProject('Custom Role');

        // Column 5 is the first column of the destination, where duplicated tasks land
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);
        $this->assertEquals(1, $columnRestrictionModel->create(2, 1, 5, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));

        $this->assertFalse($actionValidator->validateParameters(1, 2, array('column_id' => 2, 'project_id' => 2)));
    }

    public function testValidateParametersWithDestinationProjectWithMatchedColumnRestriction()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createDestinationProject('Custom Role');

        // Column 6 is "Ready" in the destination, where moved tasks from "Ready" (column 2) land
        $columnRestrictionModel = new ColumnRestrictionModel($this->container);
        $this->assertEquals(1, $columnRestrictionModel->create(2, 1, 6, ColumnRestrictionModel::RULE_BLOCK_TASK_CREATION));

        $this->assertFalse($actionValidator->validateParameters(1, 2, array('column_id' => 2, 'project_id' => 2)));
        $this->assertTrue($actionValidator->validateParameters(1, 2, array('column_id' => 1, 'project_id' => 2)));
    }

    public function testValidateParametersWithoutUserSession()
    {
        $actionValidator = new ActionValidator($this->container);
        $this->createProjects();

        // API calls made with the application credentials have no user session
        $this->assertTrue($actionValidator->validateParameters(1, 0, array('project_id' => 3)));

        // Project resources are still checked
        $this->assertFalse($actionValidator->validateParameters(1, 0, array('column_id' => 5)));
    }

    private function createProjects()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project B')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'Project C')));

        $this->assertEquals(1, $categoryModel->create(array('project_id' => 1, 'name' => 'Category A')));
        $this->assertEquals(2, $categoryModel->create(array('project_id' => 2, 'name' => 'Category B')));

        // Each project has a default swimlane with the same id as the project
        $this->assertArrayHasKey(1, $swimlaneModel->getList(1));
        $this->assertArrayHasKey(2, $swimlaneModel->getList(2));

        $this->assertTrue($projectUserRoleModel->addUser(1, 1, Role::PROJECT_MANAGER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 1, Role::PROJECT_MANAGER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(3, 3, Role::PROJECT_MANAGER));
    }

    private function createDestinationProject($destinationRole)
    {
        $projectModel = new ProjectModel($this->container);
        $projectRoleModel = new ProjectRoleModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Source')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Destination')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));

        if ($destinationRole === 'Custom Role') {
            $this->assertEquals(1, $projectRoleModel->create(2, 'Custom Role'));
        }

        $this->assertTrue($projectUserRoleModel->addUser(2, 2, $destinationRole));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'user',
        );
    }
}
