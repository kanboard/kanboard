<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Comment;
use Kanboard\Model\Project;
use Kanboard\Action\CommentCreationMoveTaskColumn;

class CommentCreationMoveTaskColumnTest extends Base
{
    public function testSuccess()
    {
        $this->container['sessionStorage']->user = array('id' => 1);

        $projectModel = new Project($this->container);
        $commentModel = new Comment($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 2));

        $action = new CommentCreationMoveTaskColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);

        $this->assertTrue($action->execute($event, Task::EVENT_MOVE_COLUMN));

        $comment = $commentModel->getById(1);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('Moved to column Ready', $comment['comment']);
    }

    public function testWithUserNotLogged()
    {
        $projectModel = new Project($this->container);
        $commentModel = new Comment($this->container);
        $taskCreationModel = new TaskCreation($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $event = new GenericEvent(array('project_id' => 1, 'task_id' => 1, 'column_id' => 3));

        $action = new CommentCreationMoveTaskColumn($this->container);
        $action->setProjectId(1);
        $action->setParam('column_id', 2);

        $this->assertFalse($action->execute($event, Task::EVENT_MOVE_COLUMN));
    }
}
