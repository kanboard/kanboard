<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TaskLinkProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test task links';
    protected $taskLinkId;
    protected $taskId1;
    protected $taskId2;

    public function testAll()
    {
        $this->assertCreateTeamProject();

        $this->taskId1 = $this->app->createTask(array('project_id' => $this->projectId, 'title' => 'Task 1'));
        $this->taskId2 = $this->app->createTask(array('project_id' => $this->projectId, 'title' => 'Task 2'));

        $this->assertNotFalse($this->taskId1);
        $this->assertNotFalse($this->taskId2);

        $this->assertCreateTaskLink();
        $this->assertGetTaskLink();
        $this->assertGetAllTaskLinks();
        $this->assertUpdateTaskLink();
        $this->assertRemoveTaskLink();
    }

    public function assertCreateTaskLink()
    {
        $this->taskLinkId = $this->app->createTaskLink($this->taskId1, $this->taskId2, 1);
        $this->assertNotFalse($this->taskLinkId);
    }

    public function assertGetTaskLink()
    {
        $link = $this->app->getTaskLinkById($this->taskLinkId);
        $this->assertNotNull($link);
        $this->assertEquals($this->taskId1, $link['task_id']);
        $this->assertEquals($this->taskId2, $link['opposite_task_id']);
        $this->assertEquals(1, $link['link_id']);
    }

    public function assertGetAllTaskLinks()
    {
        $links = $this->app->getAllTaskLinks($this->taskId2);
        $this->assertCount(1, $links);
    }

    public function assertUpdateTaskLink()
    {
        $this->assertTrue($this->app->updateTaskLink($this->taskLinkId, $this->taskId1, $this->taskId2, 3));

        $link = $this->app->getTaskLinkById($this->taskLinkId);
        $this->assertNotNull($link);
        $this->assertEquals($this->taskId1, $link['task_id']);
        $this->assertEquals($this->taskId2, $link['opposite_task_id']);
        $this->assertEquals(3, $link['link_id']);
    }

    public function assertRemoveTaskLink()
    {
        $this->assertTrue($this->app->removeTaskLink($this->taskLinkId));

        $links = $this->app->getAllTaskLinks($this->taskId2);
        $this->assertCount(0, $links);
    }
}
