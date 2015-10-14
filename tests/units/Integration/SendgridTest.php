<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Integration\Sendgrid;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\User;

class SendgridTest extends Base
{
    public function testSendEmail()
    {
        $pm = new Sendgrid($this->container);
        $pm->sendEmail('test@localhost', 'Me', 'Test', 'Content', 'Bob');

        $this->assertEquals('https://api.sendgrid.com/api/mail.send.json', $this->container['httpClient']->getUrl());

        $data = $this->container['httpClient']->getData();

        $this->assertArrayHasKey('api_user', $data);
        $this->assertArrayHasKey('api_key', $data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('fromname', $data);
        $this->assertArrayHasKey('to', $data);
        $this->assertArrayHasKey('toname', $data);
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('html', $data);

        $this->assertEquals('test@localhost', $data['to']);
        $this->assertEquals('Me', $data['toname']);
        $this->assertEquals('notifications@kanboard.local', $data['from']);
        $this->assertEquals('Bob', $data['fromname']);
        $this->assertEquals('Test', $data['subject']);
        $this->assertEquals('Content', $data['html']);
        $this->assertEquals('', $data['api_key']);
        $this->assertEquals('', $data['api_user']);
    }

    public function testHandlePayload()
    {
        $w = new Sendgrid($this->container);
        $p = new Project($this->container);
        $pp = new ProjectPermission($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(2, $u->create(array('username' => 'me', 'email' => 'me@localhost')));

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2', 'identifier' => 'TEST1')));

        // Empty payload
        $this->assertFalse($w->receiveEmail(array()));

        // Unknown user
        $this->assertFalse($w->receiveEmail(array(
            'envelope' => '{"to":["a@b.c"],"from":"a.b.c"}',
            'subject' => 'Email task'
        )));

        // Project not found
        $this->assertFalse($w->receiveEmail(array(
            'envelope' => '{"to":["a@b.c"],"from":"me@localhost"}',
            'subject' => 'Email task'
        )));

        // User is not member
        $this->assertFalse($w->receiveEmail(array(
            'envelope' => '{"to":["something+test1@localhost"],"from":"me@localhost"}',
            'subject' => 'Email task'
        )));

        $this->assertTrue($pp->addMember(2, 2));

        // The task must be created
        $this->assertTrue($w->receiveEmail(array(
            'envelope' => '{"to":["something+test1@localhost"],"from":"me@localhost"}',
            'subject' => 'Email task'
        )));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('Email task', $task['title']);
        $this->assertEquals('', $task['description']);
        $this->assertEquals(2, $task['creator_id']);

        // Html content
        $this->assertTrue($w->receiveEmail(array(
            'envelope' => '{"to":["something+test1@localhost"],"from":"me@localhost"}',
            'subject' => 'Email task',
            'html' => '<strong>bold</strong> text',
        )));

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('Email task', $task['title']);
        $this->assertEquals('**bold** text', $task['description']);
        $this->assertEquals(2, $task['creator_id']);

        // Text content
        $this->assertTrue($w->receiveEmail(array(
            'envelope' => '{"to":["something+test1@localhost"],"from":"me@localhost"}',
            'subject' => 'Email task',
            'text' => '**bold** text',
        )));

        $task = $tf->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('Email task', $task['title']);
        $this->assertEquals('**bold** text', $task['description']);
        $this->assertEquals(2, $task['creator_id']);

        // Text + html content
        $this->assertTrue($w->receiveEmail(array(
            'envelope' => '{"to":["something+test1@localhost"],"from":"me@localhost"}',
            'subject' => 'Email task',
            'html' => '<strong>bold</strong> html',
            'text' => '**bold** text',
        )));

        $task = $tf->getById(4);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('Email task', $task['title']);
        $this->assertEquals('**bold** html', $task['description']);
        $this->assertEquals(2, $task['creator_id']);
    }
}
