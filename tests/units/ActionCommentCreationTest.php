<?php

require_once __DIR__.'/Base.php';

use Event\GenericEvent;
use Model\Task;
use Model\TaskCreation;
use Model\Comment;
use Model\Project;
use Integration\GithubWebhook;

class ActionCommentCreationTest extends Base
{
    public function testWithUser()
    {
        $action = new Action\CommentCreation($this->container, 1, GithubWebhook::EVENT_ISSUE_COMMENT);

        // We create a task in the first column
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);
        $c = new Comment($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1)));

        // We create an event to move the task to the 2nd column
        $event = array(
            'project_id' => 1,
            'task_id' => 1,
            'user_id' => 1,
            'comment' => 'youpi',
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        // Our task should be updated
        $comment = $c->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('youpi', $comment['comment']);
    }
}
