<?php

namespace KanboardTests\units\Notification;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Client;
use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Subscriber\NotificationSubscriber;
use Psr\Log\LoggerInterface;

class WebhookNotificationTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('get', 'getJson', 'postJson', 'postJsonAsync', 'postForm', 'postFormAsync', 'withPrivateNetworksBlocked'))
            ->getMock();

        $this->container['blockedHttpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('postJson'))
            ->getMock();

        $this->container['httpClient']
            ->method('withPrivateNetworksBlocked')
            ->willReturn($this->container['blockedHttpClient']);

        $this->container['httpClient']
            ->expects($this->never())
            ->method('postJson');
    }

    public function testTaskCreation()
    {
        $configModel = new ConfigModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $this->container['dispatcher']->addSubscriber(new NotificationSubscriber($this->container));

        $configModel->save(array('webhook_url' => 'http://localhost/?task-creation'));

        $this->container['blockedHttpClient']
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

        $this->container['blockedHttpClient']
            ->expects($this->never())
            ->method('postJson');

        $this->container['httpClient'] = new Client($this->container);

        $this->container['logger'] = $this->createMock(LoggerInterface::class);
        $this->container['logger']
            ->expects($this->once())
            ->method('info')
            ->with($this->stringStartsWith('HttpClient: blocked request to private network (http://[::ffff:169.254.169.254]/latest/meta-data/?token='));

        $this->container['logger']
            ->expects($this->never())
            ->method('error');

        $configModel->save(array('webhook_url' => 'http://[::ffff:169.254.169.254]/latest/meta-data/'));

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

        $this->container['blockedHttpClient']
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
