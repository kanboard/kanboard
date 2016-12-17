<?php

require_once __DIR__.'/BaseProcedureTest.php';

class TaskTagProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project with tasks and tags';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertSetTaskTags();
        $this->assertGetTaskTags();
    }

    public function assertSetTaskTags()
    {
        $this->assertTrue($this->app->setTaskTags($this->projectId, $this->taskId, array('tag1', 'tag2')));
    }

    public function assertGetTaskTags()
    {
        $tags = $this->app->getTaskTags($this->taskId);
        $this->assertEquals(array('tag1', 'tag2'), array_values($tags));
    }
}
