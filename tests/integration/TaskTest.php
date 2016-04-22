<?php

require_once __DIR__.'/Base.php';

class TaskTest extends Base
{
    public function testSearchTasks()
    {
        $project_id1 = $this->app->createProject('My project');
        $project_id2 = $this->app->createProject('My project');
        $this->assertNotFalse($project_id1);
        $this->assertNotFalse($project_id2);

        $this->assertNotFalse($this->app->createTask(array('project_id' => $project_id1, 'title' => 'T1')));
        $this->assertNotFalse($this->app->createTask(array('project_id' => $project_id1, 'title' => 'T2')));
        $this->assertNotFalse($this->app->createTask(array('project_id' => $project_id2, 'title' => 'T3')));

        $tasks = $this->app->searchTasks($project_id1, 't2');
        $this->assertCount(1, $tasks);
        $this->assertEquals('T2', $tasks[0]['title']);

        $tasks = $this->app->searchTasks(array('project_id' => $project_id2, 'query' => 'assignee:nobody'));
        $this->assertCount(1, $tasks);
        $this->assertEquals('T3', $tasks[0]['title']);
    }

    public function testPriorityAttribute()
    {
        $project_id = $this->app->createProject('My project');
        $this->assertNotFalse($project_id);

        $task_id = $this->app->createTask(array('project_id' => $project_id, 'title' => 'My task', 'priority' => 2));

        $task = $this->app->getTask($task_id);
        $this->assertEquals(2, $task['priority']);

        $this->assertTrue($this->app->updateTask(array('id' => $task_id, 'project_id' => $project_id, 'priority' => 3)));

        $task = $this->app->getTask($task_id);
        $this->assertEquals(3, $task['priority']);
    }

    public function testChangeAssigneeToAssignableUser()
    {
        $project_id = $this->app->createProject('My project');
        $this->assertNotFalse($project_id);

        $user_id = $this->app->createUser('user0', 'password');
        $this->assertNotFalse($user_id);

        $this->assertTrue($this->app->addProjectUser($project_id, $user_id, 'project-member'));

        $task_id = $this->app->createTask(array('project_id' => $project_id, 'title' => 'My task'));
        $this->assertNotFalse($task_id);

        $this->assertTrue($this->app->updateTask(array('id' => $task_id, 'project_id' => $project_id, 'owner_id' => $user_id)));

        $task = $this->app->getTask($task_id);
        $this->assertEquals($user_id, $task['owner_id']);
    }

    public function testChangeAssigneeToNotAssignableUser()
    {
        $project_id = $this->app->createProject('My project');
        $this->assertNotFalse($project_id);

        $task_id = $this->app->createTask(array('project_id' => $project_id, 'title' => 'My task'));
        $this->assertNotFalse($task_id);

        $this->assertFalse($this->app->updateTask(array('id' => $task_id, 'project_id' => $project_id, 'owner_id' => 1)));

        $task = $this->app->getTask($task_id);
        $this->assertEquals(0, $task['owner_id']);
    }

    public function testChangeAssigneeToNobody()
    {
        $project_id = $this->app->createProject('My project');
        $this->assertNotFalse($project_id);

        $user_id = $this->app->createUser('user1', 'password');
        $this->assertNotFalse($user_id);

        $this->assertTrue($this->app->addProjectUser($project_id, $user_id, 'project-member'));

        $task_id = $this->app->createTask(array('project_id' => $project_id, 'title' => 'My task', 'owner_id' => $user_id));
        $this->assertNotFalse($task_id);

        $this->assertTrue($this->app->updateTask(array('id' => $task_id, 'project_id' => $project_id, 'owner_id' => 0)));

        $task = $this->app->getTask($task_id);
        $this->assertEquals(0, $task['owner_id']);
    }

    public function testMoveTaskToAnotherProject()
    {
        $project_id1 = $this->app->createProject('My project');
        $this->assertNotFalse($project_id1);

        $project_id2 = $this->app->createProject('My project');
        $this->assertNotFalse($project_id2);

        $task_id = $this->app->createTask(array('project_id' => $project_id1, 'title' => 'My task'));
        $this->assertNotFalse($task_id);

        $this->assertTrue($this->app->moveTaskToProject($task_id, $project_id2));

        $task = $this->app->getTask($task_id);
        $this->assertEquals($project_id2, $task['project_id']);
    }

    public function testMoveCopyToAnotherProject()
    {
        $project_id1 = $this->app->createProject('My project');
        $this->assertNotFalse($project_id1);

        $project_id2 = $this->app->createProject('My project');
        $this->assertNotFalse($project_id2);

        $task_id1 = $this->app->createTask(array('project_id' => $project_id1, 'title' => 'My task'));
        $this->assertNotFalse($task_id1);

        $task_id2 = $this->app->duplicateTaskToProject($task_id1, $project_id2);
        $this->assertNotFalse($task_id2);

        $task = $this->app->getTask($task_id1);
        $this->assertEquals($project_id1, $task['project_id']);

        $task = $this->app->getTask($task_id2);
        $this->assertEquals($project_id2, $task['project_id']);
    }
}
