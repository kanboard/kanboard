<?php

require_once __DIR__.'/Base.php';

use Integration\MailgunWebhook;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\ProjectPermission;
use Model\User;

class MailgunWebhookTest extends Base
{
    public function testHandlePayload()
    {
        $w = new MailgunWebhook($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(2, $u->create(array('name' => 'me', 'email' => 'me@localhost')));

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2', 'identifier' => 'TEST1')));

        // Empty payload
        $this->assertFalse($w->parsePayload(array()));

        // Unknown user
        $this->assertFalse($w->parsePayload(array('sender' => 'a@b.c', 'subject' => 'Email task', 'recipient' => 'foobar', 'stripped-text' => 'boo')));

        // Project not found
        $this->assertFalse($w->parsePayload(array('sender' => 'me@localhost', 'subject' => 'Email task', 'recipient' => 'foo+test@localhost', 'stripped-text' => 'boo')));

        // User is not member
        $this->assertFalse($w->parsePayload(array('sender' => 'me@localhost', 'subject' => 'Email task', 'recipient' => 'foo+test1@localhost', 'stripped-text' => 'boo')));
        $this->assertTrue($pp->addMember(2, 2));

        // The task must be created
        $this->assertTrue($w->parsePayload(array('sender' => 'me@localhost', 'subject' => 'Email task', 'recipient' => 'foo+test1@localhost', 'stripped-text' => 'boo')));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('Email task', $task['title']);
        $this->assertEquals('boo', $task['description']);
        $this->assertEquals(2, $task['creator_id']);
    }
}
