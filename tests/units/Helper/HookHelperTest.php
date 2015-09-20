<?php

require_once __DIR__.'/../Base.php';

use Helper\Hook;

class HookHelperTest extends Base
{
    public function testMultipleHooks()
    {
        $this->container['template'] = $this
            ->getMockBuilder('\Core\Template')
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
}