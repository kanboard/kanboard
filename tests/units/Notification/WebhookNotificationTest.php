<?php

namespace KanboardTests\units\Notification;

use KanboardTests\units\Base;
use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Subscriber\NotificationSubscriber;

class WebhookNotificationTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('get', 'getJson', 'postJson', 'postJsonAsync', 'postForm', 'postFormAsync', 'isPrivateURL'))
            ->getMock();
    }

    public function testTaskCreation()
    {
        $configModel = new ConfigModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $this->container['dispatcher']->addSubscriber(new NotificationSubscriber($this->container));

        $configModel->save(array('webhook_url' => 'http://localhost/?task-creation'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('isPrivateURL')
            ->willReturn(false);

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postJson')
            ->with($this->stringContains('http://localhost/?task-creation&token='), $this->anything());

        $projectId = $projectModel->create(array('name' => 'test'));
        $this->assertNotFalse($projectId);
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => $projectId, 'title' => 'test')));
    }

    public function testWebhookBlockedForPrivateNetwork()
    {
        $configModel = new ConfigModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $this->container['dispatcher']->addSubscriber(new NotificationSubscriber($this->container));

        $configModel->save(array('webhook_url' => 'http://192.168.1.1/webhook'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('isPrivateURL')
            ->with('http://192.168.1.1/webhook')
            ->willReturn(true);

        $this->container['httpClient']
            ->expects($this->never())
            ->method('postJson');

        $projectId = $projectModel->create(array('name' => 'test'));
        $this->assertNotFalse($projectId);
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => $projectId, 'title' => 'test')));
    }

    public function testWebhookDisablesRedirects()
    {
        $configModel = new ConfigModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $this->container['dispatcher']->addSubscriber(new NotificationSubscriber($this->container));

        $configModel->save(array('webhook_url' => 'https://example.com/webhook'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('isPrivateURL')
            ->willReturn(false);

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postJson')
            ->with(
                $this->stringContains('https://example.com/webhook'),
                $this->anything(),
                $this->identicalTo([]),
                $this->identicalTo(false),
                $this->identicalTo(false)
            );

        $projectId = $projectModel->create(array('name' => 'test'));
        $this->assertNotFalse($projectId);
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => $projectId, 'title' => 'test')));
    }
}
