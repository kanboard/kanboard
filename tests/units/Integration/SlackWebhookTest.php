<?php

require_once __DIR__.'/../Base.php';

use Integration\SlackWebhook;
use Model\Project;
use Model\Task;

class SlackWebhookTest extends Base
{
    public function testIsActivatedFromGlobalConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook'))
            ->will($this->returnValue(1));

        $this->assertTrue($slack->isActivated(1));
    }

    public function testIsActivatedFromProjectConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['projectIntegration'] = $this
            ->getMockBuilder('\Model\ProjectIntegration')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'hasValue',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook'))
            ->will($this->returnValue(0));

        $this->container['projectIntegration']
            ->expects($this->once())
            ->method('hasValue')
            ->with(
                $this->equalTo(1),
                $this->equalTo('slack'),
                $this->equalTo(1)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($slack->isActivated(1));
    }

    public function testIsNotActivated()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['projectIntegration'] = $this
            ->getMockBuilder('\Model\ProjectIntegration')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'hasValue',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook'))
            ->will($this->returnValue(0));

        $this->container['projectIntegration']
            ->expects($this->once())
            ->method('hasValue')
            ->with(
                $this->equalTo(1),
                $this->equalTo('slack'),
                $this->equalTo(1)
            )
            ->will($this->returnValue(false));

        $this->assertFalse($slack->isActivated(1));
    }

    public function testGetChannelFromGlobalConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook_channel'))
            ->will($this->returnValue('mychannel'));

        $this->assertEquals('mychannel', $slack->getChannel(1));
    }

    public function testGetChannelFromProjectConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['projectIntegration'] = $this
            ->getMockBuilder('\Model\ProjectIntegration')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getParameters',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook_channel'))
            ->will($this->returnValue(''));

        $this->container['projectIntegration']
            ->expects($this->once())
            ->method('getParameters')
            ->with($this->equalTo(1))
            ->will($this->returnValue(array('slack_webhook_channel' => 'my_project_channel')));

        $this->assertEquals('my_project_channel', $slack->getChannel(1));
    }

    public function testGetWebhoookUrlFromGlobalConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook'))
            ->will($this->returnValue(1));

        $this->container['config']
            ->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook_url'))
            ->will($this->returnValue('url'));

        $this->assertEquals('url', $slack->getWebhookUrl(1));
    }

    public function testGetWebhookUrlFromProjectConfig()
    {
        $slack = new SlackWebhook($this->container);

        $this->container['config'] = $this
            ->getMockBuilder('\Model\Config')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'get',
            ))
            ->getMock();

        $this->container['projectIntegration'] = $this
            ->getMockBuilder('\Model\ProjectIntegration')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getParameters',
            ))
            ->getMock();

        $this->container['config']
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('integration_slack_webhook'))
            ->will($this->returnValue(0));

        $this->container['projectIntegration']
            ->expects($this->once())
            ->method('getParameters')
            ->with($this->equalTo(1))
            ->will($this->returnValue(array('slack_webhook_url' => 'my_project_url')));

        $this->assertEquals('my_project_url', $slack->getWebhookUrl(1));
    }

    public function testSendPayloadWithChannel()
    {
        $this->container['httpClient'] = $this
            ->getMockBuilder('\Core\HttpClient')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'postJson',
            ))
            ->getMock();

        $slack = $this
            ->getMockBuilder('\Integration\SlackWebhook')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getChannel',
                'getWebhookUrl',
            ))
            ->getMock();

        $slack
            ->expects($this->at(0))
            ->method('getChannel')
            ->with(
                $this->equalTo(1)
            )
            ->will($this->returnValue('mychannel'));

        $slack
            ->expects($this->at(1))
            ->method('getWebhookUrl')
            ->with(
                $this->equalTo(1)
            )
            ->will($this->returnValue('url'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postJson')
            ->with(
                $this->equalTo('url'),
                $this->equalTo(array('text' => 'test', 'channel' => 'mychannel'))
            );

        $slack->sendPayload(1, array('text' => 'test'));
    }

    public function testSendPayloadWithoutChannel()
    {
        $this->container['httpClient'] = $this
            ->getMockBuilder('\Core\HttpClient')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'postJson',
            ))
            ->getMock();

        $slack = $this
            ->getMockBuilder('\Integration\SlackWebhook')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getChannel',
                'getWebhookUrl',
            ))
            ->getMock();

        $slack
            ->expects($this->at(0))
            ->method('getChannel')
            ->with(
                $this->equalTo(1)
            )
            ->will($this->returnValue(''));

        $slack
            ->expects($this->at(1))
            ->method('getWebhookUrl')
            ->with(
                $this->equalTo(1)
            )
            ->will($this->returnValue('url'));

        $this->container['httpClient']
            ->expects($this->once())
            ->method('postJson')
            ->with(
                $this->equalTo('url'),
                $this->equalTo(array('text' => 'test'))
            );

        $slack->sendPayload(1, array('text' => 'test'));
    }

    public function testSendMessage()
    {
        $message = 'test';

        $payload = array(
            'text' => $message,
            'username' => 'Kanboard',
            'icon_url' => 'http://kanboard.net/assets/img/favicon.png',
        );

        $slack = $this
            ->getMockBuilder('\Integration\SlackWebhook')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'sendPayload',
            ))
            ->getMock();

        $slack
            ->expects($this->once())
            ->method('sendPayload')
            ->with(
                $this->equalTo(1),
                $this->equalTo($payload)
            );

        $slack->sendMessage(1, $message);
    }

    public function testNotify()
    {
        $message = '*[foobar]* FooBar created the task #1 (task #1)';

        $this->container['session']['user'] = array('username' => 'foobar', 'name' => 'FooBar');

        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'foobar')));
        $this->assertTrue($this->container['config']->save(array('integration_slack_webhook' => 1)));

        $slack = $this
            ->getMockBuilder('\Integration\SlackWebhook')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'sendMessage',
            ))
            ->getMock();

        $slack
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->equalTo(1),
                $this->equalTo($message)
            );

        $slack->notify(1, 1, Task::EVENT_CREATE, array('task' => array('id' => 1, 'title' => 'task #1')));
    }
}
