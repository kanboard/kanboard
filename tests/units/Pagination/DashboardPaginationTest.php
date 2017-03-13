<?php

use Kanboard\Core\Security\Role;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;
use Kanboard\Pagination\DashboardPagination;

require_once __DIR__.'/../Base.php';

class DashboardPaginationTest extends Base
{
    public function testDashboardPagination()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $dashboardPagination = new DashboardPagination($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));

        $this->assertTrue($projectUserRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 2)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1)));

        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask A', 'user_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask B', 'user_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask C')));

        $dashboard = $dashboardPagination->getOverview(1);
        $this->assertCount(1, $dashboard);
        $this->assertEquals(1, $dashboard[0]['project_id']);
        $this->assertEquals('Project #1', $dashboard[0]['project_name']);
        $this->assertEquals(1, $dashboard[0]['paginator']->getTotal());

        $tasks = $dashboard[0]['paginator']->getCollection();
        $this->assertCount(1, $tasks);
        $this->assertCount(1, $tasks[0]['subtasks']);
        $this->assertEquals('subtask B', $tasks[0]['subtasks'][0]['title']);
    }

    public function testWhenUserIsNotAssignedToTask()
    {
        $userModel = new UserModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $dashboardPagination = new DashboardPagination($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));

        $this->assertTrue($projectUserRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 2, 'priority' => 3)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 2, 'owner_id' => 2)));

        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask A', 'user_id' => 2)));
        $this->assertEquals(2, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask B', 'user_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask C')));

        $dashboard = $dashboardPagination->getOverview(1);
        $this->assertCount(1, $dashboard);
        $this->assertEquals(1, $dashboard[0]['project_id']);
        $this->assertEquals('Project #1', $dashboard[0]['project_name']);
        $this->assertEquals(1, $dashboard[0]['paginator']->getTotal());

        $tasks = $dashboard[0]['paginator']->getCollection();
        $this->assertCount(1, $tasks);
        $this->assertCount(1, $tasks[0]['subtasks']);
        $this->assertEquals('subtask B', $tasks[0]['subtasks'][0]['title']);

        $dashboard = $dashboardPagination->getOverview(2);
        $this->assertCount(2, $dashboard);
        $this->assertEquals('Project #1', $dashboard[0]['project_name']);
        $this->assertEquals('Project #2', $dashboard[1]['project_name']);
        $this->assertEquals(1, $dashboard[0]['paginator']->getTotal());

        $tasks = $dashboard[0]['paginator']->getCollection();
        $this->assertCount(1, $tasks);
        $this->assertCount(0, $tasks[0]['subtasks']);

        $tasks = $dashboard[1]['paginator']->getCollection();
        $this->assertCount(2, $tasks);
        $this->assertCount(1, $tasks[0]['subtasks']);
        $this->assertEquals('subtask A', $tasks[0]['subtasks'][0]['title']);
    }

    public function testWhenColumnIsHidden()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $columnModel = new ColumnModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $dashboardPagination = new DashboardPagination($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 1, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask #1', 'user_id' => 1)));

        $this->assertCount(1, $dashboardPagination->getOverview(1));

        $this->assertTrue($columnModel->update(1, 'test', 0, '', 1));
        $this->assertCount(0, $dashboardPagination->getOverview(1));
    }
}
