<?php

require_once __DIR__.'/BaseProcedureTest.php';

class MeProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My private project';

    public function testAll()
    {
        $this->assertGetMe();
        $this->assertCreateMyPrivateProject();
        $this->assertGetMyProjectsList();
        $this->assertGetMyProjects();
        $this->assertCreateTask();
        $this->assertGetMyDashboard();
        $this->assertGetMyActivityStream();
    }

    public function assertGetMe()
    {
        $profile = $this->user->getMe();
        $this->assertEquals('user', $profile['username']);
        $this->assertEquals('app-user', $profile['role']);
    }

    public function assertCreateMyPrivateProject()
    {
        $this->projectId = $this->user->createMyPrivateProject($this->projectName);
        $this->assertNotFalse($this->projectId);
    }

    public function assertGetMyProjectsList()
    {
        $projects = $this->user->getMyProjectsList();
        $this->assertNotEmpty($projects);
        $this->assertEquals($this->projectName, $projects[$this->projectId]);
    }

    public function assertGetMyProjects()
    {
        $projects = $this->user->getMyProjects();
        $this->assertNotEmpty($projects);
    }

    public function assertCreateTask()
    {
        $taskId = $this->user->createTask(array('title' => 'My task', 'project_id' => $this->projectId, 'owner_id' => $this->userUserId));
        $this->assertNotFalse($taskId);
    }

    public function assertGetMyDashboard()
    {
        $dashboard = $this->user->getMyDashboard();
        $this->assertNotEmpty($dashboard);
        $this->assertEquals('My task', $dashboard[0]['title']);
    }

    public function assertGetMyActivityStream()
    {
        $activity = $this->user->getMyActivityStream();
        $this->assertNotEmpty($activity);
    }
}
