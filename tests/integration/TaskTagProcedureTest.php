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
        $this->assertCreateTaskWithTags();
        $this->assertUpdateTaskWithTags();
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

    public function assertCreateTaskWithTags()
    {
        $this->taskId = $this->app->createTask(array(
            'title' => $this->taskTitle,
            'project_id' => $this->projectId,
            'tags' => array('tag A', 'tag B'),
        ));

        $this->assertNotFalse($this->taskId);

        $tags = $this->app->getTaskTags($this->taskId);
        $this->assertEquals(array('tag A', 'tag B'), array_values($tags));
    }

    public function assertUpdateTaskWithTags()
    {
        $this->assertTrue($this->app->updateTask(array(
            'id' => $this->taskId,
            'tags' => array('tag C'),
        )));

        $tags = $this->app->getTaskTags($this->taskId);
        $this->assertEquals(array('tag C'), array_values($tags));
    }
}
