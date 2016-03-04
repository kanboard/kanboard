<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Color;
use Kanboard\Model\Config;

class ColorTest extends Base
{
    public function testFind()
    {
        $colorModel = new Color($this->container);
        $this->assertEquals('yellow', $colorModel->find('yellow'));
        $this->assertEquals('yellow', $colorModel->find('Yellow'));
        $this->assertEquals('dark_grey', $colorModel->find('Dark Grey'));
        $this->assertEquals('dark_grey', $colorModel->find('dark_grey'));
    }

    public function testGetColorProperties()
    {
        $colorModel = new Color($this->container);
        $expected = array(
            'name' => 'Light Green',
            'background' => '#dcedc8',
            'border' => '#689f38',
        );

        $this->assertEquals($expected, $colorModel->getColorProperties('light_green'));

        $expected = array(
            'name' => 'Yellow',
            'background' => 'rgb(245, 247, 196)',
            'border' => 'rgb(223, 227, 45)',
        );

        $this->assertEquals($expected, $colorModel->getColorProperties('foobar'));
    }

    public function testGetList()
    {
        $colorModel = new Color($this->container);

        $colors = $colorModel->getList();
        $this->assertCount(16, $colors);
        $this->assertEquals('Yellow', $colors['yellow']);

        $colors = $colorModel->getList(true);
        $this->assertCount(17, $colors);
        $this->assertEquals('All colors', $colors['']);
        $this->assertEquals('Yellow', $colors['yellow']);
    }

    public function testGetDefaultColor()
    {
        $colorModel = new Color($this->container);
        $configModel = new Config($this->container);

        $this->assertEquals('yellow', $colorModel->getDefaultColor());

        $this->container['memoryCache']->flush();
        $this->assertTrue($configModel->save(array('default_color' => 'red')));
        $this->assertEquals('red', $colorModel->getDefaultColor());
    }

    public function testGetDefaultColors()
    {
        $colorModel = new Color($this->container);

        $colors = $colorModel->getDefaultColors();
        $this->assertCount(16, $colors);
    }

    public function testGetBorderColor()
    {
        $colorModel = new Color($this->container);
        $this->assertEquals('rgb(74, 227, 113)', $colorModel->getBorderColor('green'));
    }

    public function testGetBackgroundColor()
    {
        $colorModel = new Color($this->container);
        $this->assertEquals('rgb(189, 244, 203)', $colorModel->getBackgroundColor('green'));
    }

    public function testGetCss()
    {
        $colorModel = new Color($this->container);
        $css = $colorModel->getCss();

        $this->assertStringStartsWith('div.color-yellow {', $css);
        $this->assertStringEndsWith('td.color-amber { background-color: #ffe082}', $css);
    }
}
