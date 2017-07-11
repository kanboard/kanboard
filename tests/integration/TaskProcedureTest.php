<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TaskProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test tasks';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertUpdateTask();
        $this->assertGetTaskById();
        $this->assertGetTaskByReference();
        $this->assertGetAllTasks();
        $this->assertOpenCloseTask();
    }

    public function assertUpdateTask()
    {
        $this->assertTrue($this->app->updateTask(array('id' => $this->taskId, 'color_id' => 'red')));
    }

    public function assertGetTaskById()
    {
        $task = $this->app->getTask($this->taskId);
        $this->assertNotNull($task);
        $this->assertEquals('red', $task['color_id']);
        $this->assertEquals($this->taskTitle, $task['title']);
        $this->assertArrayHasKey('url', $task);
    }

    public function assertGetTaskByReference()
    {
        $taskId = $this->app->createTask(array('title' => 'task with reference', 'project_id' => $this->projectId, 'reference' => 'test'));
        $this->assertNotFalse($taskId);

        $task = $this->app->getTaskByReference($this->projectId, 'test');
        $this->assertNotNull($task);
        $this->assertEquals($taskId, $task['id']);
    }

    public function assertGetAllTasks()
    {
        $tasks = $this->app->getAllTasks($this->projectId);
        $this->assertInternalType('array', $tasks);
        $this->assertNotEmpty($tasks);
        $this->assertArrayHasKey('url', $tasks[0]);
    }

    public function assertOpenCloseTask()
    {
        $this->assertTrue($this->app->closeTask($this->taskId));
        $this->assertTrue($this->app->openTask($this->taskId));
    }
}
