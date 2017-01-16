<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\CommentModel;

class CommentModelTest extends Base
{
    public function testCreate()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'bla bla', 'user_id' => 1)));
        $this->assertEquals(2, $commentModel->create(array('task_id' => 1, 'comment' => 'bla bla')));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals('bla bla', $comment['comment']);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('admin', $comment['username']);
        $this->assertEquals(time(), $comment['date_creation'], '', 3);
        $this->assertEquals(time(), $comment['date_modification'], '', 3);

        $comment = $commentModel->getById(2);
        $this->assertNotEmpty($comment);
        $this->assertEquals('bla bla', $comment['comment']);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(0, $comment['user_id']);
        $this->assertEquals('', $comment['username']);
        $this->assertEquals(time(), $comment['date_creation'], '', 3);
        $this->assertEquals(time(), $comment['date_modification'], '', 3);
    }

    public function testGetAll()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'c1', 'user_id' => 1)));
        $this->assertEquals(2, $commentModel->create(array('task_id' => 1, 'comment' => 'c2', 'user_id' => 1)));
        $this->assertEquals(3, $commentModel->create(array('task_id' => 1, 'comment' => 'c3', 'user_id' => 1)));

        $comments = $commentModel->getAll(1);

        $this->assertNotEmpty($comments);
        $this->assertEquals(3, count($comments));
        $this->assertEquals(1, $comments[0]['id']);
        $this->assertEquals(2, $comments[1]['id']);
        $this->assertEquals(3, $comments[2]['id']);

        $this->assertEquals(3, $commentModel->count(1));
    }

    public function testUpdate()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'c1', 'user_id' => 1)));
        $this->assertTrue($commentModel->update(array('id' => 1, 'comment' => 'bla')));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals('bla', $comment['comment']);
        $this->assertEquals(time(), $comment['date_modification'], '', 3);
    }

    public function testRemove()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'c1', 'user_id' => 1)));

        $this->assertTrue($commentModel->remove(1));
        $this->assertFalse($commentModel->remove(1));
        $this->assertFalse($commentModel->remove(1111));
    }

    public function testGetProjectId()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'c1', 'user_id' => 1)));

        $this->assertEquals(1, $commentModel->getProjectId(1));
        $this->assertSame(0, $commentModel->getProjectId(2));
    }
}
