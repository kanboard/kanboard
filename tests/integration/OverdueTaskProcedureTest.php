<?php

require_once __DIR__.'/BaseProcedureTest.php';

class OverdueTaskProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test overdue tasks';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateOverdueTask();
        $this->assertGetOverdueTasksByProject();
        $this->assertGetOverdueTasks();
    }

    public function assertCreateOverdueTask()
    {
        $this->assertNotFalse($this->app->createTask(array(
            'title' => 'overdue task',
            'project_id' => $this->projectId,
            'date_due' => date('Y-m-d H:i', strtotime('-2days')),
        )));
    }

    public function assertGetOverdueTasksByProject()
    {
        $tasks = $this->app->getOverdueTasksByProject($this->projectId);
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('overdue task', $tasks[0]['title']);
        $this->assertEquals($this->projectName, $tasks[0]['project_name']);
    }

    public function assertGetOverdueTasks()
    {
        $tasks = $this->app->getOverdueTasks();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('overdue task', $tasks[0]['title']);
        $this->assertEquals($this->projectName, $tasks[0]['project_name']);
    }
}
