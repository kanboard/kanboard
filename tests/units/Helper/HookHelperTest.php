<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\Hook;

class HookHelperTest extends Base
{
    public function testMultipleHooks()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Kanboard\Core\Template')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('render'))
            ->getMock();

        $this->container['template']
            ->expects($this->at(0))
            ->method('render')
            ->with(
                $this->equalTo('tpl1'),
                $this->equalTo(array())
            )
            ->will($this->returnValue('tpl1_content'));

        $this->container['template']
            ->expects($this->at(1))
            ->method('render')
            ->with(
                $this->equalTo('tpl2'),
                $this->equalTo(array())
            )
            ->will($this->returnValue('tpl2_content'));

        $h = new Hook($this->container);
        $h->attach('test', 'tpl1');
        $h->attach('test', 'tpl2');
        $this->assertEquals('tpl1_contenttpl2_content', $h->render('test'));
    }

    public function testAssetHooks()
    {
        $this->container['helper']->asset = $this
            ->getMockBuilder('\Kanboard\Helper\Asset')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('css', 'js'))
            ->getMock();

        $this->container['helper']
            ->asset
            ->expects($this->at(0))
            ->method('css')
            ->with(
                $this->equalTo('skin.css')
            )
            ->will($this->returnValue('<link rel="stylesheet" href="skin.css"></link>'));

        $this->container['helper']
            ->asset
            ->expects($this->at(1))
            ->method('js')
            ->with(
                $this->equalTo('skin.js')
            )
            ->will($this->returnValue('<script src="skin.js"></script>'));

        $h = new Hook($this->container);
        $h->attach('test1', 'skin.css');
        $h->attach('test2', 'skin.js');

        $this->assertContains('<link rel="stylesheet" href="skin.css"></link>', $h->asset('css', 'test1'));
        $this->assertContains('<script src="skin.js"></script>', $h->asset('js', 'test2'));
    }
}
