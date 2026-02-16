<?php

namespace KanboardTests\integration;

class SubtaskProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test subtasks';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertCreateSubtask();
        $this->assertGetSubtask();
        $this->assertUpdateSubtask();
        $this->assertGetAllSubtasks();
        $this->assertRemoveSubtask();
    }

    public function assertGetSubtask()
    {
        $subtask = $this->app->getSubtask($this->subtaskId);
        $this->assertEquals($this->taskId, $subtask['task_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
    }

    public function assertUpdateSubtask()
    {
        $this->assertTrue($this->app->execute('updateSubtask', array(
            'id' => $this->subtaskId,
            'task_id' => $this->taskId,
            'title' => 'test',
        )));

        $subtask = $this->app->getSubtask($this->subtaskId);
        $this->assertEquals('test', $subtask['title']);
    }

    public function assertGetAllSubtasks()
    {
        $subtasks = $this->app->getAllSubtasks($this->taskId);
        $this->assertCount(1, $subtasks);
        $this->assertEquals('test', $subtasks[0]['title']);
    }

    public function assertRemoveSubtask()
    {
        $this->assertTrue($this->app->removeSubtask($this->subtaskId));

        $subtasks = $this->app->getAllSubtasks($this->taskId);
        $this->assertCount(0, $subtasks);
    }

    public function testUpdateSubtaskCannotModifySubtaskFromAnotherProjectWithForgedTaskId()
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

        $taskIdA = $this->manager->createTask('Task A', $projectIdA);
        $taskIdB = $this->manager->createTask('Task B', $projectIdB);
        $subtaskIdB = $this->manager->createSubtask($taskIdB, 'Project B subtask');

        $this->assertNotFalse($taskIdA);
        $this->assertNotFalse($taskIdB);
        $this->assertNotFalse($subtaskIdB);

        $this->assertFalse($this->user->execute('updateSubtask', array(
            'id' => $subtaskIdB,
            'task_id' => $taskIdA,
            'title' => 'Hacked title',
            'status' => 2,
        )));

        $subtask = $this->manager->getSubtask($subtaskIdB);
        $this->assertEquals($taskIdB, $subtask['task_id']);
        $this->assertEquals('Project B subtask', $subtask['title']);
    }
}
