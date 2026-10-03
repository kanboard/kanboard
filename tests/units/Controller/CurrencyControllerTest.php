<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\CurrencyController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use KanboardTests\units\Base;

class CurrencyControllerTest extends Base
{
    public function testChangingTheCurrencyDoesNotSaveOtherSettings()
    {
        $this->container['request'] = new Request(
            $this->container,
            array('REQUEST_METHOD' => 'POST'),
            array(),
            array(
                'csrf_token' => $this->container['token']->getCSRFToken(),
                'application_currency' => 'EUR',
                'calendar_project_tasks' => '1 OR 1=1 OR 1',
            ),
            array(),
            array()
        );

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('redirect'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('redirect');

        (new CurrencyController($this->container))->update();

        $this->assertEquals('EUR', $this->container['configModel']->getOption('application_currency'));
        $this->assertEquals('date_started', $this->container['configModel']->getOption('calendar_project_tasks'));
    }
}
