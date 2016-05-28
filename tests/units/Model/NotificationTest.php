<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\CommentModel;
use Kanboard\Model\TaskFileModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\NotificationModel;
use Kanboard\Subscriber\NotificationSubscriber;

class NotificationTest extends Base
{
    public function testGetTitle()
    {
        $wn = new NotificationModel($this->container);
        $p = new ProjectModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $s = new SubtaskModel($this->container);
        $c = new CommentModel($this->container);
        $f = new TaskFileModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'test', 'task_id' => 1)));
        $this->assertEquals(1, $c->create(array('comment' => 'test', 'task_id' => 1, 'user_id' => 1)));
        $this->assertEquals(1, $f->create(1, 'test', 'blah', 123));

        $task = $tf->getDetails(1);
        $subtask = $s->getById(1, true);
        $comment = $c->getById(1);
        $file = $c->getById(1);

        $this->assertNotEmpty($task);
        $this->assertNotEmpty($subtask);
        $this->assertNotEmpty($comment);
        $this->assertNotEmpty($file);

        foreach (NotificationSubscriber::getSubscribedEvents() as $event_name => $values) {
            $title = $wn->getTitleWithoutAuthor($event_name, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array()
            ));

            $this->assertNotEmpty($title);

            $title = $wn->getTitleWithAuthor('foobar', $event_name, array(
                'task' => $task,
                'comment' => $comment,
                'subtask' => $subtask,
                'file' => $file,
                'changes' => array()
            ));

            $this->assertNotEmpty($title);
        }

        $this->assertNotEmpty($wn->getTitleWithoutAuthor(TaskModel::EVENT_OVERDUE, array('tasks' => array(array('id' => 1)))));
        $this->assertNotEmpty($wn->getTitleWithoutAuthor('unkown', array()));
    }
}
