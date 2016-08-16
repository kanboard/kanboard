<?php

require_once __DIR__.'/BaseProcedureTest.php';

class ProjectFileProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test project files';
    protected $fileId;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateProjectFile();
        $this->assertGetProjectFile();
        $this->assertDownloadProjectFile();
        $this->assertGetAllFiles();
        $this->assertRemoveProjectFile();
        $this->assertRemoveAllProjectFiles();
    }

    public function assertCreateProjectFile()
    {
        $this->fileId = $this->app->createProjectFile($this->projectId, 'My file.txt', base64_encode('plain text file'));
        $this->assertNotFalse($this->fileId);
    }

    public function assertGetProjectFile()
    {
        $file = $this->app->getProjectFile($this->projectId, $this->fileId);
        $this->assertNotEmpty($file);
        $this->assertEquals('My file.txt', $file['name']);
    }

    public function assertDownloadProjectFile()
    {
        $content = $this->app->downloadProjectFile($this->projectId, $this->fileId);
        $this->assertNotEmpty($content);
        $this->assertEquals('plain text file', base64_decode($content));
    }

    public function assertGetAllFiles()
    {
        $files = $this->app->getAllProjectFiles($this->projectId);
        $this->assertCount(1, $files);
        $this->assertEquals('My file.txt', $files[0]['name']);
    }

    public function assertRemoveProjectFile()
    {
        $this->assertTrue($this->app->removeProjectFile($this->projectId, $this->fileId));

        $files = $this->app->getAllProjectFiles($this->projectId);
        $this->assertEmpty($files);
    }

    public function assertRemoveAllProjectFiles()
    {
        $this->assertCreateProjectFile();
        $this->assertCreateProjectFile();

        $this->assertTrue($this->app->removeAllProjectFiles($this->projectId));

        $files = $this->app->getAllProjectFiles($this->projectId);
        $this->assertEmpty($files);
    }
}
