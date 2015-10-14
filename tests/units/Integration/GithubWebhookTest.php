<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Integration\GithubWebhook;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User;

class GithubWebhookTest extends Base
{
    public function testIssueOpened()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_OPENED, array($this, 'onIssueOpened'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_opened.json'), true)
        ));
    }

    public function testIssueAssigned()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, array($this, 'onIssueAssigned'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_assigned.json'), true)
        ));
    }

    public function testIssueAssignedWithNoExistingTask()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_assigned.json'), true);

        $this->assertFalse($g->handleIssueAssigned($payload['issue']));
    }

    public function testIssueAssignedWithNoExistingUser()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_assigned.json'), true);

        $this->assertFalse($g->handleIssueAssigned($payload['issue']));
    }

    public function testIssueAssignedWithNoMember()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_assigned.json'), true);

        $this->assertFalse($g->handleIssueAssigned($payload['issue']));
    }

    public function testIssueAssignedWithMember()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_assigned.json'), true);

        $this->assertTrue($g->handleIssueAssigned($payload['issue']));
    }

    public function testIssueUnassigned()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE, array($this, 'onIssueUnassigned'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_unassigned.json'), true)
        ));
    }

    public function testIssueClosed()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_CLOSED, array($this, 'onIssueClosed'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_closed.json'), true)
        ));
    }

    public function testIssueClosedWithTaskNotFound()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_closed.json'), true);

        $this->assertFalse($g->handleIssueClosed($payload['issue']));
    }

    public function testIssueReopened()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_REOPENED, array($this, 'onIssueReopened'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_reopened.json'), true)
        ));
    }

    public function testIssueReopenedWithTaskNotFound()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_reopened.json'), true);

        $this->assertFalse($g->handleIssueReopened($payload['issue']));
    }

    public function testIssueLabeled()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_LABEL_CHANGE, array($this, 'onIssueLabeled'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_labeled.json'), true)
        ));
    }

    public function testIssueLabeledWithTaskNotFound()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_labeled.json'), true);

        $this->assertFalse($g->handleIssueLabeled($payload['issue'], $payload['label']));
    }

    public function testIssueUnLabeled()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_LABEL_CHANGE, array($this, 'onIssueUnlabeled'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issues',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_unlabeled.json'), true)
        ));
    }

    public function testIssueUnLabeledWithTaskNotFound()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $payload = json_decode(file_get_contents(__DIR__.'/../fixtures/github_issue_unlabeled.json'), true);

        $this->assertFalse($g->handleIssueUnlabeled($payload['issue'], $payload['label']));
    }

    public function testCommentCreatedWithNoUser()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNoUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue_comment',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithNotMember()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithNotMember'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue_comment',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_comment_created.json'), true)
        ));
    }

    public function testCommentCreatedWithUser()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_ISSUE_COMMENT, array($this, 'onCommentCreatedWithUser'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'reference' => 3, 'project_id' => 1)));

        $u = new User($this->container);
        $this->assertEquals(2, $u->create(array('username' => 'fguillot')));

        $pp = new ProjectPermission($this->container);
        $this->assertTrue($pp->addMember(1, 2));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'issue_comment',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_comment_created.json'), true)
        ));
    }

    public function testPush()
    {
        $this->container['dispatcher']->addListener(GithubWebhook::EVENT_COMMIT, array($this, 'onPush'));

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));

        $tc = new TaskCreation($this->container);
        $this->assertEquals(1, $tc->create(array('title' => 'boo', 'project_id' => 1)));

        $g = new GithubWebhook($this->container);
        $g->setProjectId(1);

        $this->assertNotFalse($g->parsePayload(
            'push',
            json_decode(file_get_contents(__DIR__.'/../fixtures/github_push.json'), true)
        ));
    }

    public function onIssueOpened($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(3, $data['reference']);
        $this->assertEquals('Test Webhook', $data['title']);
        $this->assertEquals("plop\n\n[Github Issue](https://github.com/kanboardapp/webhook/issues/3)", $data['description']);
    }

    public function onIssueAssigned($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
        $this->assertEquals(2, $data['owner_id']);
    }

    public function onIssueUnassigned($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
        $this->assertEquals(0, $data['owner_id']);
    }

    public function onIssueClosed($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
    }

    public function onIssueReopened($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
    }

    public function onIssueLabeled($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
        $this->assertEquals('bug', $data['label']);
    }

    public function onIssueUnlabeled($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(3, $data['reference']);
        $this->assertEquals('bug', $data['label']);
        $this->assertEquals(0, $data['category_id']);
    }

    public function onCommentCreatedWithNoUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(113834672, $data['reference']);
        $this->assertEquals("test\n\n[By @fguillot on Github](https://github.com/kanboardapp/webhook/issues/3#issuecomment-113834672)", $data['comment']);
    }

    public function onCommentCreatedWithNotMember($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(0, $data['user_id']);
        $this->assertEquals(113834672, $data['reference']);
        $this->assertEquals("test\n\n[By @fguillot on Github](https://github.com/kanboardapp/webhook/issues/3#issuecomment-113834672)", $data['comment']);
    }

    public function onCommentCreatedWithUser($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals(2, $data['user_id']);
        $this->assertEquals(113834672, $data['reference']);
        $this->assertEquals("test\n\n[By @fguillot on Github](https://github.com/kanboardapp/webhook/issues/3#issuecomment-113834672)", $data['comment']);
    }

    public function onPush($event)
    {
        $data = $event->getAll();
        $this->assertEquals(1, $data['project_id']);
        $this->assertEquals(1, $data['task_id']);
        $this->assertEquals('boo', $data['title']);
        $this->assertEquals("Update README to fix #1\n\n[Commit made by @fguillot on Github](https://github.com/kanboardapp/webhook/commit/98dee3e49ee7aa66ffec1f761af93da5ffd711f6)", $data['commit_comment']);
        $this->assertEquals('Update README to fix #1', $data['commit_message']);
        $this->assertEquals('https://github.com/kanboardapp/webhook/commit/98dee3e49ee7aa66ffec1f761af93da5ffd711f6', $data['commit_url']);
    }
}
