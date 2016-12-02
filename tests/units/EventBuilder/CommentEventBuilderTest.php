<?php

use Kanboard\EventBuilder\CommentEventBuilder;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class CommentEventBuilderTest extends Base
{
    public function testWithMissingComment()
    {
        $commentEventBuilder = new CommentEventBuilder($this->container);
        $commentEventBuilder->withCommentId(42);
        $this->assertNull($commentEventBuilder->buildEvent());
    }

    public function testBuild()
    {
        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $commentEventBuilder = new CommentEventBuilder($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'bla bla', 'user_id' => 1)));

        $commentEventBuilder->withCommentId(1);
        $event = $commentEventBuilder->buildEvent();

        $this->assertInstanceOf('Kanboard\Event\CommentEvent', $event);
        $this->assertNotEmpty($event['comment']);
        $this->assertNotEmpty($event['task']);
        $this->assertEquals(1, $event->getTaskId());
        $this->assertEquals(1, $event->getProjectId());
    }
}
