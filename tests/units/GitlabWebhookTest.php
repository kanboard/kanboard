<?php

require_once __DIR__.'/Base.php';

use Integration\GitlabWebhook;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;

class GitlabWebhookTest extends Base
{
    private $push_payload = '{"before":"9187f41ba34a2b40d41c50ed4b624ce374c5e583","after":"b3caaee62ad27dc31497946065ac18299784aee4","ref":"refs/heads/master","user_id":74067,"user_name":"Fred","project_id":124474,"repository":{"name":"kanboard","url":"git@gitlab.com:minicoders/kanboard.git","description":"Test repo","homepage":"https://gitlab.com/minicoders/kanboard"},"commits":[{"id":"b3caaee62ad27dc31497946065ac18299784aee4","message":"Fix bug #2\n","timestamp":"2014-12-28T20:31:48-05:00","url":"https://gitlab.com/minicoders/kanboard/commit/b3caaee62ad27dc31497946065ac18299784aee4","author":{"name":"Frédéric Guillot","email":"git@localhost"}}],"total_commits_count":1}';
    private $issue_open_payload = '{"object_kind":"issue","user":{"name":"Fred","username":"minicoders","avatar_url":"https://secure.gravatar.com/avatar/3c44936e5a56f80711bff14987d2733f?s=40\u0026d=identicon"},"object_attributes":{"id":103356,"title":"Test Webhook","assignee_id":null,"author_id":74067,"project_id":124474,"created_at":"2014-12-29 01:24:24 UTC","updated_at":"2014-12-29 01:24:24 UTC","position":0,"branch_name":null,"description":"- test1\r\n- test2","milestone_id":null,"state":"opened","iid":1,"url":"https://gitlab.com/minicoders/kanboard/issues/1","action":"open"}}';
    private $issue_closed_payload = '{"object_kind":"issue","user":{"name":"Fred","username":"minicoders","avatar_url":"https://secure.gravatar.com/avatar/3c44936e5a56f80711bff14987d2733f?s=40\u0026d=identicon"},"object_attributes":{"id":103361,"title":"uu","assignee_id":null,"author_id":74067,"project_id":124474,"created_at":"2014-12-29 01:28:44 UTC","updated_at":"2014-12-29 01:34:47 UTC","position":0,"branch_name":null,"description":"","milestone_id":null,"state":"closed","iid":4,"url":"https://gitlab.com/minicoders/kanboard/issues/4","action":"update"}}';

    public function testGetEventType()
    {
        $g = new GitlabWebhook($this->container);

        $this->assertEquals(GitlabWebhook::TYPE_PUSH, $g->getType(json_decode($this->push_payload, true)));
        $this->assertEquals(GitlabWebhook::TYPE_ISSUE, $g->getType(json_decode($this->issue_open_payload, true)));
        $this->assertEquals(GitlabWebhook::TYPE_ISSUE, $g->getType(json_decode($this->issue_closed_payload, true)));
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

        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_COMMIT, function() {});

        $event = json_decode($this->push_payload, true);

        // No task
        $this->assertFalse($g->handleCommit($event['commits'][0]));

        // Create task with the wrong id
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertFalse($g->handleCommit($event['commits'][0]));

        // Create task with the right id
        $this->assertEquals(2, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($g->handleCommit($event['commits'][0]));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(GitlabWebhook::EVENT_COMMIT.'.closure', $called);
    }

    public function testHandleIssueOpened()
    {
        $g = new GitlabWebhook($this->container);
        $g->setProjectId(1);

        $this->container['dispatcher']->addListener(GitlabWebhook::EVENT_ISSUE_OPENED, array($this, 'onOpen'));

        $event = json_decode($this->issue_open_payload, true);
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

        $event = json_decode($this->issue_closed_payload, true);

        // Issue not there
        $this->assertFalse($g->handleIssueClosed($event['object_attributes']));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertEmpty($called);

        // Create a task with the issue reference
        $this->assertEquals(1, $tc->create(array('title' => 'A', 'project_id' => 1, 'reference' => 103361)));
        $task = $tf->getByReference(103361);
        $this->assertNotEmpty($task);

        $this->assertTrue($g->handleIssueClosed($event['object_attributes']));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(GitlabWebhook::EVENT_ISSUE_CLOSED.'.GitlabWebhookTest::onClose', $called);
    }

    public function onOpen($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(103356, $data['reference']);
        $this->assertEquals('Test Webhook', $data['title']);
        $this->assertEquals("- test1\r\n- test2\n\n[Gitlab Issue](https://gitlab.com/minicoders/kanboard/issues/1)", $data['description']);
    }

    public function onClose($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(103361, $data['reference']);
    }
}
