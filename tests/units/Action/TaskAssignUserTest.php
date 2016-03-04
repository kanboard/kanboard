<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\User;
use Kanboard\Action\TaskAssignUser;
use Kanboard\Core\Security\Role;

class TaskAssignUserTest extends Base
{
    public function testChangeUser()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'owner_id' => 0)));
        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'owner_id' => 2));

        $action = new TaskAssignUser($this->container);
        $action->setProjectId(1);
        $action->addEvent('test.event', 'Test Event');

        $this->assertTrue($action->execute($event, 'test.event'));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['owner_id']);
    }

    public function testWithNotAssignableUser()
    {
        $userModel = new User($this->container);
        $projectModel = new Project($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskFinderModel = new TaskFinder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'owner_id' => 1));

        $action = new TaskAssignUser($this->container);
        $action->setProjectId(1);
        $action->addEvent('test.event', 'Test Event');

        $this->assertFalse($action->execute($event, 'test.event'));
    }
}
