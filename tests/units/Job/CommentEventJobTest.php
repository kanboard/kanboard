<?php

use Kanboard\Job\CommentEventJob;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class CommentEventJobTest extends Base
{
    public function testJobParams()
    {
        $commentEventJob = new CommentEventJob($this->container);
        $commentEventJob->withParams(123, 'foobar');

        $this->assertSame(array(123, 'foobar'), $commentEventJob->getJobParams());
    }

    public function testWithMissingComment()
    {
        $this->container['dispatcher']->addListener(CommentModel::EVENT_CREATE, function() {});

        $commentEventJob = new CommentEventJob($this->container);
        $commentEventJob->execute(42, CommentModel::EVENT_CREATE);

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);
    }

    public function testTriggerEvents()
    {
        $this->container['dispatcher']->addListener(CommentModel::EVENT_CREATE, function() {});
        $this->container['dispatcher']->addListener(CommentModel::EVENT_UPDATE, function() {});
        $this->container['dispatcher']->addListener(CommentModel::EVENT_DELETE, function() {});

        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => 'foobar', 'user_id' => 1)));
        $this->assertTrue($commentModel->update(array('id' => 1, 'comment' => 'test')));
        $this->assertTrue($commentModel->remove(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(CommentModel::EVENT_CREATE.'.closure', $called);
        $this->assertArrayHasKey(CommentModel::EVENT_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(CommentModel::EVENT_DELETE.'.closure', $called);
    }

    public function testThatUserMentionJobIsCalled()
    {
        $comment = 'some comment';

        $this->container['queueManager'] = $this
            ->getMockBuilder('\Kanboard\Core\Queue\QueueManager')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'push',
            ))
            ->getMock();

        $this->container['userMentionJob'] = $this
            ->getMockBuilder('\Kanboard\Job\UserMentionJob')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'withParams',
            ))
            ->getMock();

        $this->container['queueManager']
            ->expects($this->any())
            ->method('push');

        $this->container['userMentionJob']
            ->expects($this->once())
            ->method('withParams')
            ->with($comment, CommentModel::EVENT_USER_MENTION, $this->anything())
            ->will($this->returnValue($this->container['userMentionJob']));

        $commentModel = new CommentModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $commentEventJob = new CommentEventJob($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'comment' => $comment, 'user_id' => 1)));

        $commentEventJob->execute(1, CommentModel::EVENT_CREATE);
    }
}
