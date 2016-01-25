<?php

require_once __DIR__.'/Base.php';

class ProjectPermissionTest extends Base
{
    public function testGetProjectUsers()
    {
        $this->assertNotFalse($this->app->createProject('Test'));
        $this->assertNotFalse($this->app->createGroup('Test'));

        $projectId = $this->getProjectId();
        $groupId = $this->getGroupId();

        $this->assertTrue($this->app->addGroupMember($projectId, $groupId));
        $this->assertSame(array(), $this->app->getProjectUsers($projectId));
    }

    public function testProjectUser()
    {
        $projectId = $this->getProjectId();
        $this->assertTrue($this->app->addProjectUser($projectId, 1));

        $users = $this->app->getProjectUsers($projectId);
        $this->assertCount(1, $users);
        $this->assertEquals('admin', $users[1]);

        $users = $this->app->getAssignableUsers($projectId);
        $this->assertCount(1, $users);
        $this->assertEquals('admin', $users[1]);

        $this->assertTrue($this->app->changeProjectUserRole($projectId, 1, 'project-viewer'));

        $users = $this->app->getAssignableUsers($projectId);
        $this->assertCount(0, $users);

        $this->assertTrue($this->app->removeProjectUser($projectId, 1));
        $this->assertSame(array(), $this->app->getProjectUsers($projectId));
    }

    public function testProjectGroup()
    {
        $projectId = $this->getProjectId();
        $groupId = $this->getGroupId();

        $this->assertTrue($this->app->addProjectGroup($projectId, $groupId));

        $users = $this->app->getProjectUsers($projectId);
        $this->assertCount(1, $users);
        $this->assertEquals('admin', $users[1]);

        $users = $this->app->getAssignableUsers($projectId);
        $this->assertCount(1, $users);
        $this->assertEquals('admin', $users[1]);

        $this->assertTrue($this->app->changeProjectGroupRole($projectId, $groupId, 'project-viewer'));

        $users = $this->app->getAssignableUsers($projectId);
        $this->assertCount(0, $users);

        $this->assertTrue($this->app->removeProjectGroup($projectId, 1));
        $this->assertSame(array(), $this->app->getProjectUsers($projectId));
    }
}
