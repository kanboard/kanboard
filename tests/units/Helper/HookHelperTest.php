<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\HookHelper;

class HookHelperTest extends Base
{
    public function testAttachCallable()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Kanboard\Core\Template')
            ->setConstructorArgs(array($this->container['helper']))
            ->setMethods(array('render'))
            ->getMock();

        $this->container['template']
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('tpl1'),
                $this->equalTo(array('k0' => 'v0', 'k1' => 'v1'))
            )
            ->will($this->returnValue('tpl1_content'));

        $hookHelper = new HookHelper($this->container);
        $hookHelper->attachCallable('test', 'tpl1', function() {
            return array(
                'k1' => 'v1',
            );
        });

        $this->assertEquals('tpl1_content', $hookHelper->render('test', array('k0' => 'v0')));
    }

    public function testAttachCallableWithNoResult()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Kanboard\Core\Template')
            ->setConstructorArgs(array($this->container['helper']))
            ->setMethods(array('render'))
            ->getMock();

        $this->container['template']
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('tpl1'),
                $this->equalTo(array('k0' => 'v0'))
            )
            ->will($this->returnValue('tpl1_content'));

        $hookHelper = new HookHelper($this->container);
        $hookHelper->attachCallable('test', 'tpl1', function() {
        });

        $this->assertEquals('tpl1_content', $hookHelper->render('test', array('k0' => 'v0')));
    }

    public function testAttachLocalVariables()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Kanboard\Core\Template')
            ->setConstructorArgs(array($this->container['helper']))
            ->setMethods(array('render'))
            ->getMock();

        $this->container['template']
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('tpl1'),
                $this->equalTo(array('k0' => 'v0', 'k1' => 'v1'))
            )
            ->will($this->returnValue('tpl1_content'));

        $hookHelper = new HookHelper($this->container);
        $hookHelper->attach('test', 'tpl1', array('k1' => 'v1'));
        $this->assertEquals('tpl1_content', $hookHelper->render('test', array('k0' => 'v0')));
    }

    public function testMultipleHooks()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Kanboard\Core\Template')
            ->setConstructorArgs(array($this->container['helper']))
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

        $hookHelper = new HookHelper($this->container);
        $hookHelper->attach('test', 'tpl1');
        $hookHelper->attach('test', 'tpl2');
        $this->assertEquals('tpl1_contenttpl2_content', $hookHelper->render('test'));
    }

    public function testAssetHooks()
    {
        $this->container['helper']->asset = $this
            ->getMockBuilder('\Kanboard\Helper\AssetHelper')
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

        $hookHelper = new HookHelper($this->container);
        $hookHelper->attach('test1', 'skin.css');
        $hookHelper->attach('test2', 'skin.js');

        $this->assertContains('<link rel="stylesheet" href="skin.css"></link>', $hookHelper->asset('css', 'test1'));
        $this->assertContains('<script src="skin.js"></script>', $hookHelper->asset('js', 'test2'));
    }
}
