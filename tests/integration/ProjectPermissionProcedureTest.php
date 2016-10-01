<?php

require_once __DIR__.'/BaseProcedureTest.php';

class ProjectPermissionProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'Project with permission';
    protected $username = 'user-project-permission';
    protected $groupName1 = 'My group A for project permission';
    protected $groupName2 = 'My group B for project permission';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateGroups();
        $this->assertCreateUser();

        $this->assertAddProjectUser();
        $this->assertGetProjectUsers();
        $this->assertGetAssignableUsers();
        $this->assertChangeProjectUserRole();
        $this->assertRemoveProjectUser();

        $this->assertAddProjectGroup();
        $this->assertGetProjectUsers();
        $this->assertGetAssignableUsers();
        $this->assertChangeProjectGroupRole();
        $this->assertRemoveProjectGroup();
    }

    public function assertAddProjectUser()
    {
        $this->assertTrue($this->app->addProjectUser($this->projectId, $this->userId));
    }

    public function assertGetProjectUsers()
    {
        $members = $this->app->getProjectUsers($this->projectId);
        $this->assertCount(1, $members);
        $this->assertArrayHasKey($this->userId, $members);
        $this->assertEquals($this->username, $members[$this->userId]);
    }

    public function assertGetAssignableUsers()
    {
        $members = $this->app->getAssignableUsers($this->projectId);
        $this->assertCount(1, $members);
        $this->assertArrayHasKey($this->userId, $members);
        $this->assertEquals($this->username, $members[$this->userId]);
    }

    public function assertChangeProjectUserRole()
    {
        $this->assertTrue($this->app->changeProjectUserRole($this->projectId, $this->userId, 'project-viewer'));

        $members = $this->app->getAssignableUsers($this->projectId);
        $this->assertCount(0, $members);
    }

    public function assertRemoveProjectUser()
    {
        $this->assertTrue($this->app->removeProjectUser($this->projectId, $this->userId));

        $members = $this->app->getProjectUsers($this->projectId);
        $this->assertCount(0, $members);
    }

    public function assertAddProjectGroup()
    {
        $this->assertTrue($this->app->addGroupMember($this->groupId1, $this->userId));
        $this->assertTrue($this->app->addProjectGroup($this->projectId, $this->groupId1));
    }

    public function assertChangeProjectGroupRole()
    {
        $this->assertTrue($this->app->changeProjectGroupRole($this->projectId, $this->groupId1, 'project-viewer'));

        $members = $this->app->getAssignableUsers($this->projectId);
        $this->assertCount(0, $members);
    }

    public function assertRemoveProjectGroup()
    {
        $this->assertTrue($this->app->removeProjectGroup($this->projectId, $this->groupId1));

        $members = $this->app->getProjectUsers($this->projectId);
        $this->assertCount(0, $members);
    }
}
