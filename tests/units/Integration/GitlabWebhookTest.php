<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Integration\GitlabWebhook;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User;

class GitlabWebhookTest extends Base
{
    public function testGetEventType()
    {
        $g = new GitlabWebhook($this->container);

        $this->assertEquals(GitlabWebhook::TYPE_PUSH, $g->getType(json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_push.json'), true)));
        $this->assertEquals(GitlabWebhook::TYPE_ISSUE, $g->getType(json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_issue_opened.json'), true)));
        $this->assertEquals(GitlabWebhook::TYPE_COMMENT, $g->getType(json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_comment_created.json'), true)));
        $this->assertEquals('', $g->getType(array()));
    }

    public function testHandleCommit()
    {
        $g = new GitlabWebhook($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $g->setProjectId(1);

        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_COMMIT, array($this, 'onCommit'));

        $event = json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_push.json'), true);

        // No task
        $this->assertFalse($g->handleCommit($event['commits'][0]));

        // Create task with the wrong id
        $this->assertEquals(1, $tc->create(array('title' => 'test1', 'project_id' => 1)));
        $this->assertFalse($g->handleCommit($event['commits'][0]));

        // Create task with the right id
        $this->assertEquals(2, $tc->create(array('title' => 'test2', 'project_id' => 1)));
        $this->assertTrue($g->handleCommit($event['commits'][0]));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(GitlabWebhook::EVENT_COMMIT.'.GitlabWebhookTest::onCommit', $called);
    }

    public function testHandleIssueOpened()
    {
        $g = new GitlabWebhook($this->container);
        $g->setProjectId(1);

        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_OPENED, array($this, 'onOpen'));

        $event = json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_issue_opened.json'), true);
        $this->assertTrue($g->handleIssueOpened($event['object_attributes']));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(GitlabWebhook::EVENT_ISSUE_OPENED.'.GitlabWebhookTest::onOpen', $called);
    }

    public function testHandleIssueClosed()
    {
        $g = new GitlabWebhook($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $g->setProjectId(1);

        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_CLOSED, array($this, 'onClose'));

        $event = json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_issue_closed.json'), true);

        // Issue not there
        $this->assertFalse($g->handleIssueClosed($event['object_attributes']));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);

        // Create a task with the issue reference
        $this->assertEquals(1, $tc->create(array('title' => 'A', 'project_id' => 1, 'reference' => 355691)));
        $task = $tf->getByReference(1, 355691);
        $this->assertNotEmpty($task);

        $task = $tf->getByReference(2, 355691);
        $this->assertEmpty($task);

        $this->assertTrue($g->handleIssueClosed($event['object_attributes']));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(GitlabWebhook::EVENT_ISSUE_CLOSED.'.GitlabWebhookTest::onClose', $called);
    }

    public function testCommentCreatedWithNoUser()
    {
        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNoUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 355691, 'project_id' => 1)));

        $g = new GitlabWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithNotMember()
    {
        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNotMember'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 355691, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'minicoders')));

        $g = new GitlabWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithUser()
    {
        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 355691, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'minicoders')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new GitlabWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            json_decode(file_get_contents(__DIR__.'/../fixtures/gitlab_comment_created.json'), true)
        ));
    }

    public function onOpen($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(355691, $data['reference']);
        $this->assertEquals('Bug', $data['title']);
        $this->assertEquals("There is a bug somewhere.\r\n\r\nBye\n\n[Gitlab Issue](https://gitlab.com/minicoders/test-webhook/issues/1)", $data['description']);
    }

    public function onClose($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(355691, $data['reference']);
    }

    public function onCommit($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(2, $data['task_id']);
        $this->assertEquals('test2', $data['title']);
        $this->assertEquals("Fix bug #2\n\n[Commit made by @Fred on Gitlab](https://gitlab.com/minicoders/test-webhook/commit/48aafa75eef9ad253aa254b0c82c987a52ebea78)", $data['commit_comment']);
        $this->assertEquals("Fix bug #2", $data['commit_message']);
        $this->assertEquals('https://gitlab.com/minicoders/test-webhook/commit/48aafa75eef9ad253aa254b0c82c987a52ebea78', $data['commit_url']);
    }

    public function onCommentCreatedWithNoUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(1642761, $data['reference']);
        $this->assertEquals("Super comment!\n\n[By @minicoders on Gitlab](https://gitlab.com/minicoders/test-webhook/issues/1#note_1642761)", $data['comment']);
    }

    public function onCommentCreatedWithNotMember($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(1642761, $data['reference']);
        $this->assertEquals("Super comment!\n\n[By @minicoders on Gitlab](https://gitlab.com/minicoders/test-webhook/issues/1#note_1642761)", $data['comment']);
    }

    public function onCommentCreatedWithUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(2, $data['user_id']);
        $this->assertEquals(1642761, $data['reference']);
        $this->assertEquals("Super comment!\n\n[By @minicoders on Gitlab](https://gitlab.com/minicoders/test-webhook/issues/1#note_1642761)", $data['comment']);
    }
}
