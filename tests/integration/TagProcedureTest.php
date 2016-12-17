<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TagProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project with tags';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTag();
        $this->assertGetProjectTags();
        $this->assertGetAllTags();
        $this->assertUpdateTag();
        $this->assertRemoveTag();
    }

    public function assertCreateTag()
    {
        $this->assertNotFalse($this->app->createTag($this->projectId, 'some tag'));
    }

    public function assertGetProjectTags()
    {
        $tags = $this->app->getTagsByProject($this->projectId);
        $this->assertCount(1, $tags);
        $this->assertEquals('some tag', $tags[0]['name']);
    }

    public function assertGetAllTags()
    {
        $tags = $this->app->getAllTags();
        $this->assertCount(1, $tags);
        $this->assertEquals('some tag', $tags[0]['name']);
    }

    public function assertUpdateTag()
    {
        $tags = $this->app->getAllTags();
        $this->assertCount(1, $tags);
        $this->assertTrue($this->app->updateTag($tags[0]['id'], 'another tag'));

        $tags = $this->app->getAllTags();
        $this->assertCount(1, $tags);
        $this->assertEquals('another tag', $tags[0]['name']);
    }

    public function assertRemoveTag()
    {
        $tags = $this->app->getAllTags();
        $this->assertCount(1, $tags);
        $this->assertTrue($this->app->removeTag($tags[0]['id']));

        $tags = $this->app->getAllTags();
        $this->assertCount(0, $tags);
    }
}
