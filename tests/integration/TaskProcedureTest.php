<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TaskProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test tasks';
    protected $metaKey = 'MyTestMetaKey';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertUpdateTask();
        $this->assertGetTaskById();
        $this->assertGetTaskByReference();
        $this->assertGetAllTasks();
        $this->assertOpenCloseTask();
        $this->assertSaveTaskMetadata();
        $this->assertGetTaskMetadata();
        $this->assertGetTaskMetadataByName();
        $this->assertRemoveTaskMetadata():
    }

    public function assertSaveTaskMetadata()
    {
        $createMetaKey = $this->app->saveTaskMetadata($this->taskId,array($this->metaKey => 'metaValue1'));
        $this->assertTrue($createMetaKey);
    }
    
    public function assertGetTaskMetadata()
    {
        $createMetaKey = $this->app->saveTaskMetadata($this->taskId,array($this->metaKey => 'metaValue1'));
        $this->assertTrue($createMetaKey,'Did not create metakey with success');
        $metaData = $this->app->getTaskMetadata(($this->taskId);
        $this->assertArrayHasKey($this->metaKey, $metaData);
        $this->assertEquals('metaValue1', $metaData[$this->metaKey]);
    }
    
    public function assertGetTaskMetadataByName()
    {
        $createMetaKey = $this->app->saveTaskMetadata($this->taskId,array($this->metaKey => 'metaValue1'));
        $this->assertTrue($createMetaKey,'Did not create metakey with success');
        $metaValue = $this->app->getTaskMetadataByName($this->taskId,$this->metaKey);
        $this->assertEquals('metaValue1', $metaValue, 'Did not return correct metadata value');
    }
    
    public function assertRemoveTaskMetadata()
    {
        $createMetaKey = $this->app->saveTaskMetadata($this->taskId,array($this->metaKey => 'metaValue1'));
        $this->assertTrue($createMetaKey,'Did not create metakey with success');
        $metaValue = $this->app->removeTaskMetadata($this->taskId,$this->metaKey);
        $this->assertTrue($metaValue,'Did not remove metakey with success');
        $metaValue = $this->app->getTaskMetadataByName($this->taskId,$this->metaKey);
        $this->assertEquals('',$metaValue,'Did not return an empty string due to metadata being deleted');
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
    }

    public function assertOpenCloseTask()
    {
        $this->assertTrue($this->app->closeTask($this->taskId));
        $this->assertTrue($this->app->openTask($this->taskId));
    }
}
