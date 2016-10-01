<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use Kanboard\Analytic\UserDistributionAnalytic;
use Kanboard\Core\Security\Role;

class UserDistributionAnalyticTest extends Base
{
    public function testBuild()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);
        $userDistributionModel = new UserDistributionAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4')));

        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 4, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 5, Role::PROJECT_MEMBER));

        $this->createTasks(0, 10, 1);
        $this->createTasks(2, 30, 1);
        $this->createTasks(3, 40, 1);
        $this->createTasks(4, 10, 1);
        $this->createTasks(5, 10, 1);

        $expected = array(
            array(
                'user' => 'Unassigned',
                'nb_tasks' => 10,
                'percentage' => 10.0,
            ),
            array(
                'user' => 'user1',
                'nb_tasks' => 30,
                'percentage' => 30.0,
            ),
            array(
                'user' => 'user2',
                'nb_tasks' => 40,
                'percentage' => 40.0,
            ),
            array(
                'user' => 'user3',
                'nb_tasks' => 10,
                'percentage' => 10.0,
            ),
            array(
                'user' => 'user4',
                'nb_tasks' => 10,
                'percentage' => 10.0,
            )
        );

        $this->assertEquals($expected, $userDistributionModel->build(1));
    }

    private function createTasks($user_id, $nb_active, $nb_inactive)
    {
        $taskCreationModel = new TaskCreationModel($this->container);

        for ($i = 0; $i < $nb_active; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'owner_id' => $user_id, 'is_active' => 1)));
        }

        for ($i = 0; $i < $nb_inactive; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'owner_id' => $user_id, 'is_active' => 0)));
        }
    }
}
