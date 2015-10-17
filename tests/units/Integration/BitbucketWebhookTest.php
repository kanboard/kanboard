<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Integration\BitbucketWebhook;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User;

class BitbucketWebhookTest extends Base
{
    public function testHandlePush()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_COMMIT, array($this, 'onCommit'));

        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);
        $bw = new BitbucketWebhook($this->container);
        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_push.json'), true);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $bw->setProjectId(1);

        // No task
        $this->assertFalse($bw->handlePush($payload));

        // Create task with the wrong id
        $this->assertEquals(1, $tc->create(array('title' => 'test1', 'project_id' => 1)));
        $this->assertFalse($bw->handlePush($payload));

        // Create task with the right id
        $this->assertEquals(2, $tc->create(array('title' => 'test2', 'project_id' => 1)));
        $this->assertTrue($bw->handlePush($payload));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(BitbucketWebhook::EVENT_COMMIT.'.BitbucketWebhookTest::onCommit', $called);
    }

    public function testIssueOpened()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_OPENED, array($this, 'onIssueOpened'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $bw = new BitbucketWebhook($this->container);
        $bw->setProjectId(1);

        $this->assertNotFalse($bw->parsePayload(
            'issue:created',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_opened.json'), true)
        ));
    }

    public function testCommentCreatedWithNoUser()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNoUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:comment_created',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithNotMember()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNotMember'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:comment_created',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithUser()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'minicoders')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:comment_created',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_comment_created.json'), true)
        ));
    }

    public function testIssueClosed()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_CLOSED, array($this, 'onIssueClosed'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_closed.json'), true)
        ));
    }

    public function testIssueClosedWithNoTask()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_CLOSED, array($this, 'onIssueClosed'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 42, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_closed.json'), true)
        ));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testIssueReopened()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_REOPENED, array($this, 'onIssueReopened'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_reopened.json'), true)
        ));
    }

    public function testIssueReopenedWithNoTask()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_REOPENED, array($this, 'onIssueReopened'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 42, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_reopened.json'), true)
        ));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testIssueUnassigned()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, array($this, 'onIssueUnassigned'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_unassigned.json'), true)
        ));
    }

    public function testIssueAssigned()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, array($this, 'onIssueAssigned'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'minicoders')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_assigned.json'), true)
        ));

        $this->assertNotEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testIssueAssignedWithNoPermission()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, function () {});

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'minicoders')));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_assigned.json'), true)
        ));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testIssueAssignedWithNoUser()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, function () {});

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 1, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_assigned.json'), true)
        ));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testIssueAssignedWithNoTask()
    {
        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, function () {});

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 43, 'project_id' => 1)));

        $g = new BitbucketWebhook($this->container);
        $g->setProjectId(1);

        $this->assertFalse($g->parsePayload(
            'issue:updated',
            json_decode(file_get_contents(__DIR__.'/../fixtures/bitbucket_issue_assigned.json'), true)
        ));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function onCommit($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(2, $data['task_id']);
        $this->assertEquals('test2', $data['title']);
        $this->assertEquals("Test another commit #2\n\n\n[Commit made by @Frederic Guillot on Bitbucket](https://bitbucket.org/minicoders/test-webhook/commits/824059cce7667d3f8d8780cc707391be821e0ea6)", $data['commit_comment']);
        $this->assertEquals("Test another commit #2\n", $data['commit_message']);
        $this->assertEquals('https://bitbucket.org/minicoders/test-webhook/commits/824059cce7667d3f8d8780cc707391be821e0ea6', $data['commit_url']);
    }

    public function onIssueOpened($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['reference']);
        $this->assertEquals('My new issue', $data['title']);
        $this->assertEquals("**test**\n\n[Bitbucket Issue](https://bitbucket.org/minicoders/test-webhook/issue/1/my-new-issue)", $data['description']);
    }

    public function onCommentCreatedWithNoUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(19176252, $data['reference']);
        $this->assertEquals("1. step1\n2. step2\n\n[By @Frederic Guillot on Bitbucket](https://bitbucket.org/minicoders/test-webhook/issue/1#comment-19176252)", $data['comment']);
    }

    public function onCommentCreatedWithNotMember($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(19176252, $data['reference']);
        $this->assertEquals("1. step1\n2. step2\n\n[By @Frederic Guillot on Bitbucket](https://bitbucket.org/minicoders/test-webhook/issue/1#comment-19176252)", $data['comment']);
    }

    public function onCommentCreatedWithUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(2, $data['user_id']);
        $this->assertEquals(19176252, $data['reference']);
        $this->assertEquals("1. step1\n2. step2\n\n[By @Frederic Guillot on Bitbucket](https://bitbucket.org/minicoders/test-webhook/issue/1#comment-19176252)", $data['comment']);
    }

    public function onIssueClosed($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(1, $data['reference']);
    }

    public function onIssueReopened($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(1, $data['reference']);
    }

    public function onIssueAssigned($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(1, $data['reference']);
        $this->assertEquals(2, $data['owner_id']);
    }

    public function onIssueUnassigned($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(1, $data['reference']);
        $this->assertEquals(0, $data['owner_id']);
    }
}
