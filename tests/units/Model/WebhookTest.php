<?php

require_once __DIR__.'/../Base.php';

use Model\Config;
use Model\Task;
use Model\TaskCreation;
use Model\TaskModification;
use Model\Project;
use Model\Comment;
use Subscriber\WebhookSubscriber;

class WebhookTest extends Base
{
    public function testTaskCreation()
    {
        $c = new Config($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $this->container['dispatcher']->addSubscriber(new WebhookSubscriber($this->container));

        $c->save(array('webhook_url' => 'http://localhost/?task-creation'));

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertStringStartsWith('http://localhost/?task-creation&token=', $this->container['httpClient']->getUrl());

        $event = $this->container['httpClient']->getData();
        $this->assertNotEmpty($event);
        $this->assertArrayHasKey('event_name', $event);
        $this->assertArrayHasKey('event_data', $event);
        $this->assertEquals('task.create', $event['event_name']);
        $this->assertNotEmpty($event['event_data']);

        $this->assertArrayHasKey('project_id', $event['event_data']);
        $this->assertArrayHasKey('task_id', $event['event_data']);
        $this->assertArrayHasKey('title', $event['event_data']);
        $this->assertArrayHasKey('column_id', $event['event_data']);
        $this->assertArrayHasKey('color_id', $event['event_data']);
        $this->assertArrayHasKey('swimlane_id', $event['event_data']);
        $this->assertArrayHasKey('date_creation', $event['event_data']);
        $this->assertArrayHasKey('date_modification', $event['event_data']);
        $this->assertArrayHasKey('date_moved', $event['event_data']);
        $this->assertArrayHasKey('position', $event['event_data']);
    }

    public function testTaskModification()
    {
        $c = new Config($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tm = new TaskModification($this->container);
        $this->container['dispatcher']->addSubscriber(new WebhookSubscriber($this->container));

        $c->save(array('webhook_url' => 'http://localhost/modif/'));

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertTrue($tm->update(array('id' => 1, 'title' => 'test update')));

        $this->assertStringStartsWith('http://localhost/modif/?token=', $this->container['httpClient']->getUrl());

        $event = $this->container['httpClient']->getData();
        $this->assertNotEmpty($event);
        $this->assertArrayHasKey('event_name', $event);
        $this->assertArrayHasKey('event_data', $event);
        $this->assertEquals('task.update', $event['event_name']);
        $this->assertNotEmpty($event['event_data']);

        $this->assertArrayHasKey('project_id', $event['event_data']);
        $this->assertArrayHasKey('task_id', $event['event_data']);
        $this->assertArrayHasKey('title', $event['event_data']);
        $this->assertArrayHasKey('column_id', $event['event_data']);
        $this->assertArrayHasKey('color_id', $event['event_data']);
        $this->assertArrayHasKey('swimlane_id', $event['event_data']);
        $this->assertArrayHasKey('date_creation', $event['event_data']);
        $this->assertArrayHasKey('date_modification', $event['event_data']);
        $this->assertArrayHasKey('date_moved', $event['event_data']);
        $this->assertArrayHasKey('position', $event['event_data']);
    }

    public function testCommentCreation()
    {
        $c = new Config($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $cm = new Comment($this->container);
        $this->container['dispatcher']->addSubscriber(new WebhookSubscriber($this->container));

        $c->save(array('webhook_url' => 'http://localhost/comment'));

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $cm->create(array('task_id' => 1, 'comment' => 'test comment', 'user_id' => 1)));

        $this->assertStringStartsWith('http://localhost/comment?token=', $this->container['httpClient']->getUrl());

        $event = $this->container['httpClient']->getData();
        $this->assertNotEmpty($event);
        $this->assertArrayHasKey('event_name', $event);
        $this->assertArrayHasKey('event_data', $event);
        $this->assertEquals('comment.create', $event['event_name']);
        $this->assertNotEmpty($event['event_data']);

        $this->assertArrayHasKey('task_id', $event['event_data']);
        $this->assertArrayHasKey('user_id', $event['event_data']);
        $this->assertArrayHasKey('comment', $event['event_data']);
        $this->assertArrayHasKey('id', $event['event_data']);
        $this->assertEquals('test comment', $event['event_data']['comment']);
    }
}
