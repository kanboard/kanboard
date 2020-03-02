<?php

use Kanboard\Core\ExternalTask\ExternalTaskManager;

require_once __DIR__.'/../../Base.php';

class ExternalTaskManagerTest extends Base
{
    public function testProviderNotFound()
    {
        $this->expectException('Kanboard\Core\ExternalTask\ProviderNotFoundException');

        $manager = new ExternalTaskManager();
        $manager->getProvider('foobar');
    }

    public function testRegister()
    {
        $provider = $this->getMockBuilder('Kanboard\Core\ExternalTask\ExternalTaskProviderInterface')->getMock();
        $provider->expects($this->any())->method('getName')->willReturn('MyProvider');

        $manager = new ExternalTaskManager();
        $manager->register($provider);

        $this->assertInstanceOf('Kanboard\Core\ExternalTask\ExternalTaskProviderInterface', $manager->getProvider('MyProvider'));
    }

    public function testGetList()
    {
        $provider1 = $this->getMockBuilder('Kanboard\Core\ExternalTask\ExternalTaskProviderInterface')->getMock();
        $provider1->expects($this->any())->method('getName')->willReturn('MyProvider1');

        $provider2 = $this->getMockBuilder('Kanboard\Core\ExternalTask\ExternalTaskProviderInterface')->getMock();
        $provider2->expects($this->any())->method('getName')->willReturn('MyProvider2');

        $manager = new ExternalTaskManager();
        $manager->register($provider1);
        $manager->register($provider2);
        $providers = $manager->getProvidersList();

        $expected = array('MyProvider1' => 'MyProvider1', 'MyProvider2' => 'MyProvider2');
        $this->assertEquals($expected, $providers);
    }

    public function testGetProviderListWithNoProviders()
    {
        $manager = new ExternalTaskManager();
        $this->assertSame(array(), $manager->getProvidersList());
    }
}
