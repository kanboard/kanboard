<?php

namespace KanboardTests\units\Job;

use KanboardTests\units\Base;
use Kanboard\Job\HttpAsyncJob;

class HttpAsyncJobTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('doRequest', 'withPrivateNetworksBlocked'))
            ->getMock();

        $this->container['blockedHttpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('doRequest'))
            ->getMock();

        $this->container['httpClient']
            ->method('withPrivateNetworksBlocked')
            ->willReturn($this->container['blockedHttpClient']);
    }

    public function testJobParams()
    {
        $this->container['httpClient']
            ->expects($this->never())
            ->method('doRequest');

        $this->container['blockedHttpClient']
            ->expects($this->never())
            ->method('doRequest');

        $job = new HttpAsyncJob($this->container);
        $job->withParams('POST', 'https://example.org/', '{}', array('X-Test: 1'), true, true);

        $this->assertSame(array('POST', 'https://example.org/', '{}', array('X-Test: 1'), true, true), $job->getJobParams());
    }

    public function testExecuteWithPrivateNetworksBlocked()
    {
        $this->container['httpClient']
            ->expects($this->never())
            ->method('doRequest');

        $this->container['blockedHttpClient']
            ->expects($this->once())
            ->method('doRequest')
            ->with('POST', 'https://example.org/', '{}', array('X-Test: 1'), false);

        $job = new HttpAsyncJob($this->container);
        $job->execute('POST', 'https://example.org/', '{}', array('X-Test: 1'), false, true);
    }

    public function testExecuteWithoutBlockingFlag()
    {
        $this->container['httpClient']
            ->expects($this->once())
            ->method('doRequest')
            ->with('POST', 'https://example.org/', '{}', array('X-Test: 1'), false);

        $this->container['blockedHttpClient']
            ->expects($this->never())
            ->method('doRequest');

        $job = new HttpAsyncJob($this->container);
        $job->execute('POST', 'https://example.org/', '{}', array('X-Test: 1'));
    }
}
