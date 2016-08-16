<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TaskExternalLinkProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test external links';
    private $linkId = 0;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertGetExternalTaskLinkTypes();
        $this->assertGetExternalTaskLinkProviderDependencies();
        $this->assertGetExternalTaskLinkProviderDependenciesWithProviderNotFound();
        $this->assertCreateExternalTaskLink();
        $this->assertUpdateExternalTaskLink();
        $this->assertGetAllExternalTaskLinks();
        $this->assertRemoveExternalTaskLink();
    }

    public function assertGetExternalTaskLinkTypes()
    {
        $expected = array(
            'auto' => 'Auto',
            'attachment' => 'Attachment',
            'file' => 'Local File',
            'weblink' => 'Web Link',
        );

        $types = $this->app->getExternalTaskLinkTypes();
        $this->assertEquals($expected, $types);
    }

    public function assertGetExternalTaskLinkProviderDependencies()
    {
        $expected = array(
            'related' => 'Related',
        );

        $dependencies = $this->app->getExternalTaskLinkProviderDependencies('weblink');

        $this->assertEquals($expected, $dependencies);
    }

    public function assertGetExternalTaskLinkProviderDependenciesWithProviderNotFound()
    {
        $this->assertFalse($this->app->getExternalTaskLinkProviderDependencies('foobar'));
    }

    public function assertCreateExternalTaskLink()
    {
        $url = 'http://localhost/document.pdf';
        $this->linkId = $this->app->createExternalTaskLink($this->taskId, $url, 'related', 'attachment');
        $this->assertNotFalse($this->linkId);

        $link = $this->app->getExternalTaskLinkById($this->taskId, $this->linkId);
        $this->assertEquals($this->linkId, $link['id']);
        $this->assertEquals($this->taskId, $link['task_id']);
        $this->assertEquals('document.pdf', $link['title']);
        $this->assertEquals($url, $link['url']);
        $this->assertEquals('related', $link['dependency']);
        $this->assertEquals(0, $link['creator_id']);
    }

    public function assertUpdateExternalTaskLink()
    {
        $this->assertTrue($this->app->updateExternalTaskLink(array(
            'task_id' => $this->taskId,
            'link_id' => $this->linkId,
            'title' => 'New title',
        )));

        $link = $this->app->getExternalTaskLinkById($this->taskId, $this->linkId);
        $this->assertEquals($this->linkId, $link['id']);
        $this->assertEquals($this->taskId, $link['task_id']);
        $this->assertEquals('New title', $link['title']);
        $this->assertEquals('related', $link['dependency']);
        $this->assertEquals(0, $link['creator_id']);
    }

    public function assertGetAllExternalTaskLinks()
    {
        $links = $this->app->getAllExternalTaskLinks($this->taskId);
        $this->assertCount(1, $links);
        $this->assertEquals($this->linkId, $links[0]['id']);
        $this->assertEquals($this->taskId, $links[0]['task_id']);
        $this->assertEquals('New title', $links[0]['title']);
        $this->assertEquals('related', $links[0]['dependency']);
        $this->assertEquals(0, $links[0]['creator_id']);
    }

    public function assertRemoveExternalTaskLink()
    {
        $this->assertTrue($this->app->removeExternalTaskLink($this->taskId, $this->linkId));
    }
}
