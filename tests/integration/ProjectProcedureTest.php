<?php

require_once __DIR__.'/BaseProcedureTest.php';

class ProjectProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My team project';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertGetProjectById();
        $this->assertGetProjectByName();
        $this->assertGetAllProjects();
        $this->assertUpdateProject();
        $this->assertGetProjectActivity();
        $this->assertGetProjectsActivity();
        $this->assertEnableDisableProject();
        $this->assertEnableDisablePublicAccess();
        $this->assertRemoveProject();
    }

    public function assertGetProjectById()
    {
        $project = $this->app->getProjectById($this->projectId);
        $this->assertNotNull($project);
        $this->assertEquals($this->projectName, $project['name']);
        $this->assertEquals('Description', $project['description']);
    }

    public function assertGetProjectByName()
    {
        $project = $this->app->getProjectByName($this->projectName);
        $this->assertNotNull($project);
        $this->assertEquals($this->projectId, $project['id']);
        $this->assertEquals($this->projectName, $project['name']);
        $this->assertEquals('Description', $project['description']);
    }

    public function assertGetAllProjects()
    {
        $projects = $this->app->getAllProjects();
        $this->assertNotEmpty($projects);
    }

    public function assertGetProjectActivity()
    {
        $activities = $this->app->getProjectActivity($this->projectId);
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function assertGetProjectsActivity()
    {
        $activities = $this->app->getProjectActivities(array('project_ids' => array($this->projectId)));
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function assertUpdateProject()
    {
        $this->assertTrue($this->app->updateProject(array('project_id' => $this->projectId, 'name' => 'test', 'description' => 'test')));

        $project = $this->app->getProjectById($this->projectId);
        $this->assertNotNull($project);
        $this->assertEquals('test', $project['name']);
        $this->assertEquals('test', $project['description']);

        $this->assertTrue($this->app->updateProject(array('project_id' => $this->projectId, 'name' => $this->projectName)));
    }

    public function assertEnableDisableProject()
    {
        $this->assertTrue($this->app->disableProject($this->projectId));
        $this->assertTrue($this->app->enableProject($this->projectId));
    }

    public function assertEnableDisablePublicAccess()
    {
        $this->assertTrue($this->app->disableProjectPublicAccess($this->projectId));
        $this->assertTrue($this->app->enableProjectPublicAccess($this->projectId));
    }

    public function assertRemoveProject()
    {
        $this->assertTrue($this->app->removeProject($this->projectId));
        $this->assertNull($this->app->getProjectById($this->projectId));
    }
}
