<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Action;
use Kanboard\Model\Project;
use Kanboard\Model\Category;
use Kanboard\Model\ProjectUserRole;
use Kanboard\Model\ProjectGroupRole;
use Kanboard\Model\ProjectDuplication;
use Kanboard\Model\User;
use Kanboard\Model\Group;
use Kanboard\Model\GroupMember;
use Kanboard\Model\Swimlane;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Core\Security\Role;

class ProjectDuplicationTest extends Base
{
    public function testGetSelections()
    {
        $projectDuplicationModel = new ProjectDuplication($this->container);
        $this->assertCount(5, $projectDuplicationModel->getOptionalSelection());
        $this->assertCount(6, $projectDuplicationModel->getPossibleSelection());
    }

    public function testGetClonedProjectName()
    {
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals('test (Clone)', $pd->getClonedProjectName('test'));

        $this->assertEquals(50, strlen($pd->getClonedProjectName(str_repeat('a', 50))));
        $this->assertEquals(str_repeat('a', 42).' (Clone)', $pd->getClonedProjectName(str_repeat('a', 50)));

        $this->assertEquals(50, strlen($pd->getClonedProjectName(str_repeat('a', 60))));
        $this->assertEquals(str_repeat('a', 42).' (Clone)', $pd->getClonedProjectName(str_repeat('a', 60)));
    }

    public function testClonePublicProject()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Public')));
        $this->assertEquals(2, $pd->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Public (Clone)', $project['name']);
        $this->assertEquals(1, $project['is_active']);
        $this->assertEquals(0, $project['is_private']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEquals(0, $project['owner_id']);
        $this->assertEmpty($project['token']);
    }

    public function testClonePrivateProject()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);
        $pp = new ProjectUserRole($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Private', 'is_private' => 1), 1, true));
        $this->assertEquals(2, $pd->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Private (Clone)', $project['name']);
        $this->assertEquals(1, $project['is_active']);
        $this->assertEquals(1, $project['is_private']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEquals(0, $project['owner_id']);
        $this->assertEmpty($project['token']);

        $this->assertEquals(Role::PROJECT_MANAGER, $pp->getUserRole(2, 1));
    }

    public function testCloneSharedProject()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Shared')));
        $this->assertTrue($p->update(array('id' => 1, 'is_public' => 1, 'token' => 'test')));

        $project = $p->getById(1);
        $this->assertEquals('test', $project['token']);
        $this->assertEquals(1, $project['is_public']);

        $this->assertEquals(2, $pd->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Shared (Clone)', $project['name']);
        $this->assertEquals('', $project['token']);
        $this->assertEquals(0, $project['is_public']);
    }

    public function testCloneInactiveProject()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Inactive')));
        $this->assertTrue($p->update(array('id' => 1, 'is_active' => 0)));

        $project = $p->getById(1);
        $this->assertEquals(0, $project['is_active']);

        $this->assertEquals(2, $pd->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Inactive (Clone)', $project['name']);
        $this->assertEquals(1, $project['is_active']);
    }

    public function testCloneProjectWithOwner()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Owner')));

        $project = $p->getById(1);
        $this->assertEquals(0, $project['owner_id']);

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission'), 1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Owner (Clone)', $project['name']);
        $this->assertEquals(1, $project['owner_id']);

        $this->assertEquals(Role::PROJECT_MANAGER, $projectUserRoleModel->getUserRole(2, 1));
    }

    public function testCloneProjectWithDifferentName()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Owner')));

        $project = $p->getById(1);
        $this->assertEquals(0, $project['owner_id']);

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission'), 1, 'Foobar'));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Foobar', $project['name']);
        $this->assertEquals(1, $project['owner_id']);
    }

    public function testCloneProjectAndForceItToBePrivate()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Owner')));

        $project = $p->getById(1);
        $this->assertEquals(0, $project['owner_id']);
        $this->assertEquals(0, $project['is_private']);

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission'), 1, 'Foobar', true));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Foobar', $project['name']);
        $this->assertEquals(1, $project['owner_id']);
        $this->assertEquals(1, $project['is_private']);
    }

    public function testCloneProjectWithCategories()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'C2', 'project_id' => 1)));
        $this->assertEquals(3, $c->create(array('name' => 'C3', 'project_id' => 1)));

        $this->assertEquals(2, $pd->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('P1 (Clone)', $project['name']);

        $categories = $c->getAll(2);
        $this->assertCount(3, $categories);
        $this->assertEquals('C1', $categories[0]['name']);
        $this->assertEquals('C2', $categories[1]['name']);
        $this->assertEquals('C3', $categories[2]['name']);
    }

    public function testCloneProjectWithUsers()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $pp = new ProjectUserRole($this->container);
        $u = new User($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1')));
        $this->assertEquals(3, $u->create(array('username' => 'user2')));
        $this->assertEquals(4, $u->create(array('username' => 'user3')));

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($pp->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($pp->addUser(1, 4, Role::PROJECT_VIEWER));

        $this->assertEquals(2, $pd->duplicate(1));

        $this->assertCount(3, $pp->getUsers(2));
        $this->assertEquals(Role::PROJECT_MANAGER, $pp->getUserRole(2, 2));
        $this->assertEquals(Role::PROJECT_MEMBER, $pp->getUserRole(2, 3));
        $this->assertEquals(Role::PROJECT_VIEWER, $pp->getUserRole(2, 4));
    }

    public function testCloneProjectWithUsersAndOverrideOwner()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $pp = new ProjectUserRole($this->container);
        $u = new User($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1')));
        $this->assertEquals(1, $p->create(array('name' => 'P1'), 2));

        $project = $p->getById(1);
        $this->assertEquals(2, $project['owner_id']);

        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($pp->addUser(1, 1, Role::PROJECT_MEMBER));

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission'), 1));

        $this->assertCount(2, $pp->getUsers(2));
        $this->assertEquals(Role::PROJECT_MANAGER, $pp->getUserRole(2, 2));
        $this->assertEquals(Role::PROJECT_MANAGER, $pp->getUserRole(2, 1));

        $project = $p->getById(2);
        $this->assertEquals(1, $project['owner_id']);
    }

    public function testCloneTeamProjectToPrivatProject()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $pp = new ProjectUserRole($this->container);
        $u = new User($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'user1')));
        $this->assertEquals(3, $u->create(array('username' => 'user2')));
        $this->assertEquals(1, $p->create(array('name' => 'P1'), 2));

        $project = $p->getById(1);
        $this->assertEquals(2, $project['owner_id']);
        $this->assertEquals(0, $project['is_private']);

        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($pp->addUser(1, 1, Role::PROJECT_MEMBER));

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission'), 3, 'My private project', true));

        $this->assertCount(1, $pp->getUsers(2));
        $this->assertEquals(Role::PROJECT_MANAGER, $pp->getUserRole(2, 3));

        $project = $p->getById(2);
        $this->assertEquals(3, $project['owner_id']);
        $this->assertEquals(1, $project['is_private']);
    }

    public function testCloneProjectWithGroups()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $pd = new ProjectDuplication($this->container);
        $userModel = new User($this->container);
        $groupModel = new Group($this->container);
        $groupMemberModel = new GroupMember($this->container);
        $projectGroupRoleModel = new ProjectGroupRole($this->container);
        $projectUserRoleModel = new ProjectUserRole($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        $this->assertEquals(1, $groupModel->create('G1'));
        $this->assertEquals(2, $groupModel->create('G2'));
        $this->assertEquals(3, $groupModel->create('G3'));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3')));

        $this->assertTrue($groupMemberModel->addUser(1, 2));
        $this->assertTrue($groupMemberModel->addUser(2, 3));
        $this->assertTrue($groupMemberModel->addUser(3, 4));

        $this->assertTrue($projectGroupRoleModel->addGroup(1, 1, Role::PROJECT_MANAGER));
        $this->assertTrue($projectGroupRoleModel->addGroup(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectGroupRoleModel->addGroup(1, 3, Role::PROJECT_VIEWER));

        $this->assertEquals(2, $pd->duplicate(1));

        $this->assertCount(3, $projectGroupRoleModel->getGroups(2));
        $this->assertEquals(Role::PROJECT_MANAGER, $projectUserRoleModel->getUserRole(2, 2));
        $this->assertEquals(Role::PROJECT_MEMBER, $projectUserRoleModel->getUserRole(2, 3));
        $this->assertEquals(Role::PROJECT_VIEWER, $projectUserRoleModel->getUserRole(2, 4));
    }

    public function testCloneProjectWithActionTaskAssignCurrentUser()
    {
        $p = new Project($this->container);
        $a = new Action($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_MOVE_COLUMN,
            'action_name' => 'TaskAssignCurrentUser',
            'params' => array('column_id' => 2),
        )));

        $this->assertEquals(2, $pd->duplicate(1));

        $actions = $a->getAllByProject(2);

        $this->assertNotEmpty($actions);
        $this->assertEquals('TaskAssignCurrentUser', $actions[0]['action_name']);
        $this->assertNotEmpty($actions[0]['params']);
        $this->assertEquals(6, $actions[0]['params']['column_id']);
    }

    public function testCloneProjectWithActionTaskAssignColorCategory()
    {
        $p = new Project($this->container);
        $a = new Action($this->container);
        $c = new Category($this->container);
        $pd = new ProjectDuplication($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        $this->assertEquals(1, $c->create(array('name' => 'C1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'C2', 'project_id' => 1)));
        $this->assertEquals(3, $c->create(array('name' => 'C3', 'project_id' => 1)));

        $this->assertEquals(1, $a->create(array(
            'project_id' => 1,
            'event_name' => Task::EVENT_CREATE_UPDATE,
            'action_name' => 'TaskAssignColorCategory',
            'params' => array('color_id' => 'blue', 'category_id' => 2),
        )));

        $this->assertEquals(2, $pd->duplicate(1));

        $actions = $a->getAllByProject(2);

        $this->assertNotEmpty($actions);
        $this->assertEquals('TaskAssignColorCategory', $actions[0]['action_name']);
        $this->assertNotEmpty($actions[0]['params']);
        $this->assertEquals('blue', $actions[0]['params']['color_id']);
        $this->assertEquals(5, $actions[0]['params']['category_id']);
    }

    public function testCloneProjectWithSwimlanes()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);
        $s = new Swimlane($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1', 'default_swimlane' => 'New Default')));

        // create initial swimlanes
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'S1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'S2')));
        $this->assertEquals(3, $s->create(array('project_id' => 1, 'name' => 'S3')));

        // create initial tasks
        $this->assertEquals(1, $tc->create(array('title' => 'T0', 'project_id' => 1, 'swimlane_id' => 0)));
        $this->assertEquals(2, $tc->create(array('title' => 'T1', 'project_id' => 1, 'swimlane_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'T2', 'project_id' => 1, 'swimlane_id' => 2)));
        $this->assertEquals(4, $tc->create(array('title' => 'T3', 'project_id' => 1, 'swimlane_id' => 3)));

        $this->assertEquals(2, $pd->duplicate(1, array('category', 'swimlane')));

        $swimlanes = $s->getAll(2);
        $this->assertCount(3, $swimlanes);
        $this->assertEquals(4, $swimlanes[0]['id']);
        $this->assertEquals('S1', $swimlanes[0]['name']);
        $this->assertEquals(5, $swimlanes[1]['id']);
        $this->assertEquals('S2', $swimlanes[1]['name']);
        $this->assertEquals(6, $swimlanes[2]['id']);
        $this->assertEquals('S3', $swimlanes[2]['name']);

        $swimlane = $s->getDefault(2);
        $this->assertEquals('New Default', $swimlane['default_swimlane']);

        // Check if tasks are NOT been duplicated
        $this->assertCount(0, $tf->getAll(2));
    }

    public function testCloneProjectWithTasks()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);
        $s = new Swimlane($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1')));

        // create initial tasks
        $this->assertEquals(1, $tc->create(array('title' => 'T1', 'project_id' => 1, 'column_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'T2', 'project_id' => 1, 'column_id' => 2)));
        $this->assertEquals(3, $tc->create(array('title' => 'T3', 'project_id' => 1, 'column_id' => 3)));

        $this->assertEquals(2, $pd->duplicate(1, array('category', 'action', 'task')));

        // Check if Tasks have been duplicated
        $tasks = $tf->getAll(2);
        $this->assertCount(3, $tasks);
        $this->assertEquals('T1', $tasks[0]['title']);
        $this->assertEquals('T2', $tasks[1]['title']);
        $this->assertEquals('T3', $tasks[2]['title']);
    }

    public function testCloneProjectWithSwimlanesAndTasks()
    {
        $p = new Project($this->container);
        $pd = new ProjectDuplication($this->container);
        $s = new Swimlane($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'P1', 'default_swimlane' => 'New Default')));

        // create initial swimlanes
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'S1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'S2')));
        $this->assertEquals(3, $s->create(array('project_id' => 1, 'name' => 'S3')));

        // create initial tasks
        $this->assertEquals(1, $tc->create(array('title' => 'T1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'T2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1)));
        $this->assertEquals(3, $tc->create(array('title' => 'T3', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));

        $this->assertEquals(2, $pd->duplicate(1, array('projectPermission', 'swimlane', 'task')));

        // Check if Swimlanes have been duplicated
        $swimlanes = $s->getAll(2);
        $this->assertCount(3, $swimlanes);
        $this->assertEquals(4, $swimlanes[0]['id']);
        $this->assertEquals('S1', $swimlanes[0]['name']);
        $this->assertEquals(5, $swimlanes[1]['id']);
        $this->assertEquals('S2', $swimlanes[1]['name']);
        $this->assertEquals(6, $swimlanes[2]['id']);
        $this->assertEquals('S3', $swimlanes[2]['name']);

        $swimlane = $s->getDefault(2);
        $this->assertEquals('New Default', $swimlane['default_swimlane']);

        // Check if Tasks have been duplicated
        $tasks = $tf->getAll(2);

        $this->assertCount(3, $tasks);
        $this->assertEquals('T1', $tasks[0]['title']);
        $this->assertEquals('T2', $tasks[1]['title']);
        $this->assertEquals('T3', $tasks[2]['title']);
    }
}
