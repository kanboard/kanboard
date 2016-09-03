<?php

use Kanboard\Core\Plugin\Directory;

require_once __DIR__.'/../../Base.php';

class DirectoryTest extends Base
{
    public function testIsCompatible()
    {
        $pluginDirectory = new Directory($this->container);
        $this->assertFalse($pluginDirectory->isCompatible(array('compatible_version' => '1.0.29'), '1.0.28'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '1.0.28'), '1.0.28'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '1.0.28'), 'master.1234'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '>=1.0.32'), 'master'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '>=1.0.32'), '1.0.32'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '>=1.0.32'), '1.0.33'));
        $this->assertTrue($pluginDirectory->isCompatible(array('compatible_version' => '>1.0.32'), '1.0.33'));
        $this->assertFalse($pluginDirectory->isCompatible(array('compatible_version' => '>1.0.32'), '1.0.32'));
    }

    public function testGetAvailablePlugins()
    {
        $plugins = array(
            array(
                'title' => 'Plugin A',
                'compatible_version' => '1.0.30',
                'remote_install' => true,
            ),
            array(
                'title' => 'Plugin B',
                'compatible_version' => '1.0.29',
                'remote_install' => false,
            ),
        );

        $this->container['httpClient']
            ->expects($this->once())
            ->method('getJson')
            ->with('api_url')
            ->will($this->returnValue($plugins));

        $pluginDirectory = new Directory($this->container);
        $availablePlugins = $pluginDirectory->getAvailablePlugins('api_url');

        $this->assertCount(1, $availablePlugins);
        $this->assertEquals('Plugin A', $availablePlugins[0]['title']);
    }
}
