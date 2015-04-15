<?php

require_once __DIR__.'/Base.php';

use Integration\BitbucketWebhook;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;

class BitbucketWebhookTest extends Base
{
    private $post_payload = '{"repository": {"website": "", "fork": false, "name": "webhooks", "scm": "git", "owner": "minicoders", "absolute_url": "/minicoders/webhooks/", "slug": "webhooks", "is_private": true}, "truncated": false, "commits": [{"node": "28569937627f", "files": [{"type": "added", "file": "README.md"}], "raw_author": "Frederic Guillot <fred@localhost>", "utctimestamp": "2015-02-09 00:57:45+00:00", "author": "Frederic Guillot", "timestamp": "2015-02-09 01:57:45", "raw_node": "28569937627fb406eeda9376a02b39581a974d4f", "parents": [], "branch": "master", "message": "first commit\\n", "revision": null, "size": -1}, {"node": "285699376274", "files": [{"type": "added", "file": "README.md"}], "raw_author": "Frederic Guillot <fred@localhost>", "utctimestamp": "2015-02-09 00:57:45+00:00", "author": "Frederic Guillot", "timestamp": "2015-02-09 01:57:45", "raw_node": "28569937627fb406eeda9376a02b39581a974d4f", "parents": [], "branch": "master", "message": "Fix #2\\n", "revision": null, "size": -1}], "canon_url": "https://bitbucket.org", "user": "minicoders"}';

    public function testHandleCommit()
    {
        $g = new BitbucketWebhook($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $g->setProjectId(1);

        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_COMMIT, function() {});

        $event = json_decode($this->post_payload, true);

        // No task
        $this->assertFalse($g->handleCommit($event['commits'][0]));

        // Create task with the wrong id
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertFalse($g->handleCommit($event['commits'][1]));

        // Create task with the right id
        $this->assertEquals(2, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertTrue($g->handleCommit($event['commits'][1]));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(BitbucketWebhook::EVENT_COMMIT.'.closure', $called);
    }

    public function testParsePayload()
    {
        $g = new BitbucketWebhook($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->container['dispatcher']->addListener(BitbucketWebhook::EVENT_COMMIT, function() {});

        $this->assertEquals(1, $p->create(array('name' => 'test')));

        $g->setProjectId(1);

        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $event = json_decode($this->post_payload, true);
        $this->assertTrue($g->parsePayload($event));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(BitbucketWebhook::EVENT_COMMIT.'.closure', $called);
    }
}
