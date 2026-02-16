<?php

namespace KanboardTests\integration;

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

    public function testUpdateTaskLinkCannotModifyLinkFromAnotherProjectWithForgedTaskId()
    {
        $projectIdA = $this->manager->createProject(array(
            'name' => 'Project A',
            'owner_id' => $this->managerUserId,
        ));

        $projectIdB = $this->manager->createProject(array(
            'name' => 'Project B',
            'owner_id' => $this->managerUserId,
        ));

        $this->assertNotFalse($projectIdA);
        $this->assertNotFalse($projectIdB);
        $this->assertTrue($this->manager->addProjectUser($projectIdA, $this->userUserId, 'project-member'));

        $taskIdA1 = $this->manager->createTask('Task A1', $projectIdA);
        $taskIdA2 = $this->manager->createTask('Task A2', $projectIdA);
        $taskIdB1 = $this->manager->createTask('Task B1', $projectIdB);
        $taskIdB2 = $this->manager->createTask('Task B2', $projectIdB);
        $taskLinkIdB = $this->manager->createTaskLink($taskIdB1, $taskIdB2, 1);

        $this->assertNotFalse($taskIdA1);
        $this->assertNotFalse($taskIdA2);
        $this->assertNotFalse($taskIdB1);
        $this->assertNotFalse($taskIdB2);
        $this->assertNotFalse($taskLinkIdB);

        $this->assertFalse($this->user->updateTaskLink($taskLinkIdB, $taskIdA1, $taskIdA2, 3));

        $taskLink = $this->manager->getTaskLinkById($taskLinkIdB);
        $this->assertEquals($taskIdB1, $taskLink['task_id']);
        $this->assertEquals($taskIdB2, $taskLink['opposite_task_id']);
        $this->assertEquals(1, $taskLink['link_id']);
    }
}
