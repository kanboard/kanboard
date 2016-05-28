<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Subscriber\NotificationSubscriber;

class WebhookTest extends Base
{
    public function testTaskCreation()
    {
        $c = new ConfigModel($this->container);
        $p = new ProjectModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $this->container['dispatcher']->addSubscriber(new NotificationSubscriber($this->container));

        $c->save(array('webhook_url' => 'http://localhost/?task-creation'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postJson')
            ->with($this->stringContains('http://localhost/?task-creation&token='), $this->anything());

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));
    }
}
