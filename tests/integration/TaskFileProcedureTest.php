<?php

namespace KanboardTests\integration;

class TaskFileProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test task files';
    protected $fileId;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertCreateTaskFile();
        $this->assertGetTaskFile();
        $this->assertDownloadTaskFile();
        $this->assertGetAllFiles();
        $this->assertRemoveTaskFile();
        $this->assertRemoveAllTaskFiles();
    }

    public function assertCreateTaskFile()
    {
        $this->fileId = $this->app->createTaskFile($this->projectId, $this->taskId, 'My file', base64_encode('plain text file'));
        $this->assertNotFalse($this->fileId);
    }

    public function testCreateTaskFileWithCreator()
    {
        $this->assertCreateTeamProject();
        $this->assertTrue($this->app->addProjectUser($this->projectId, $this->userUserId, 'project-member'));
        $this->assertCreateTask();

        $fileId = $this->app->createTaskFile($this->projectId, $this->taskId, 'My file', base64_encode('plain text file'), $this->userUserId);
        $this->assertNotFalse($fileId);
        $this->assertEquals($this->userUserId, $this->app->getTaskFile($fileId)['user_id']);

        $this->assertFalse($this->app->createTaskFile($this->projectId, $this->taskId, 'My file', base64_encode('plain text file'), $this->adminUserId));
    }

    public function assertGetTaskFile()
    {
        $file = $this->app->getTaskFile($this->fileId);
        $this->assertNotEmpty($file);
        $this->assertEquals('My file', $file['name']);
    }

    public function assertDownloadTaskFile()
    {
        $content = $this->app->downloadTaskFile($this->fileId);
        $this->assertNotEmpty($content);
        $this->assertEquals('plain text file', base64_decode($content));
    }

    public function assertGetAllFiles()
    {
        $files = $this->app->getAllTaskFiles(array('task_id' => $this->taskId));
        $this->assertCount(1, $files);
        $this->assertEquals('My file', $files[0]['name']);
    }

    public function assertRemoveTaskFile()
    {
        $this->assertTrue($this->app->removeTaskFile($this->fileId));

        $files = $this->app->getAllTaskFiles(array('task_id' => $this->taskId));
        $this->assertEmpty($files);
    }

    public function assertRemoveAllTaskFiles()
    {
        $this->assertCreateTaskFile();
        $this->assertCreateTaskFile();

        $this->assertTrue($this->app->removeAllTaskFiles($this->taskId));

        $files = $this->app->getAllTaskFiles(array('task_id' => $this->taskId));
        $this->assertEmpty($files);
    }
}
