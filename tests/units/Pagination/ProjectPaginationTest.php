<?php

use Kanboard\Core\Security\Role;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use Kanboard\Pagination\ProjectPagination;

require_once __DIR__.'/../Base.php';

class ProjectPaginationTest extends Base
{
    public function testDashboardPagination()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);
        $projectPagination = new ProjectPagination($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2', 'is_private' => 1)));
        $this->assertEquals(3, $projectModel->create(array('name' => 'Project #3')));
        $this->assertEquals(4, $projectModel->create(array('name' => 'Project #4', 'is_private' => 1)));

        $this->assertEquals(2, $userModel->create(array('username' => 'test')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 2, Role::PROJECT_MANAGER));

        $this->assertCount(2, $projectPagination->getDashboardPaginator(2, 'projects', 5)->getCollection());
        $this->assertCount(0, $projectPagination->getDashboardPaginator(3, 'projects', 5)->getCollection());
        $this->assertCount(2, $projectPagination->getDashboardPaginator(2, 'projects', 5)->setOrder(ProjectModel::TABLE.'.id')->getCollection());
        $this->assertCount(2, $projectPagination->getDashboardPaginator(2, 'projects', 5)->setOrder(ProjectModel::TABLE.'.is_private')->getCollection());
        $this->assertCount(2, $projectPagination->getDashboardPaginator(2, 'projects', 5)->setOrder(ProjectModel::TABLE.'.name')->getCollection());
    }
}
