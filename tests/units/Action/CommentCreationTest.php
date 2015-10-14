<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Comment;
use Kanboard\Model\Project;
use Kanboard\Integration\GithubWebhook;
use Kanboard\Action\CommentCreation;

class CommentCreationTest extends Base
{
    public function testWithoutRequiredParams()
    {
        $action = new CommentCreation($this->container, 1, GithubWebhook::EVENT_ISSUE_COMMENT);

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
        );

        // Our event should be executed
        $this->assertFalse($action->execute(new GenericEvent($event)));

        $comment = $c->getById(1);
        $this->assertEmpty($comment);
    }

    public function testWithCommitMessage()
    {
        $action = new CommentCreation($this->container, 1, GithubWebhook::EVENT_ISSUE_COMMENT);

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
            'commit_comment' => 'plop',
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        $comment = $c->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(0, $comment['user_id']);
        $this->assertEquals('plop', $comment['comment']);
    }

    public function testWithUser()
    {
        $action = new CommentCreation($this->container, 1, GithubWebhook::EVENT_ISSUE_COMMENT);

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

        $comment = $c->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('youpi', $comment['comment']);
    }

    public function testWithNoUser()
    {
        $action = new CommentCreation($this->container, 1, GithubWebhook::EVENT_ISSUE_COMMENT);

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
            'user_id' => 0,
            'comment' => 'youpi',
        );

        // Our event should be executed
        $this->assertTrue($action->execute(new GenericEvent($event)));

        $comment = $c->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(0, $comment['user_id']);
        $this->assertEquals('youpi', $comment['comment']);
    }
}
