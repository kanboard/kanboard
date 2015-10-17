<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Plugin\Hook;

class HookTest extends Base
{
    public function testGetListeners()
    {
        $h = new Hook;
        $this->assertEmpty($h->getListeners('myhook'));

        $h->on('myhook', 'A');
        $h->on('myhook', 'B');

        $this->assertEquals(array('A', 'B'), $h->getListeners('myhook'));
    }

    public function testExists()
    {
        $h = new Hook;
        $this->assertFalse($h->exists('myhook'));

        $h->on('myhook', 'A');

        $this->assertTrue($h->exists('myhook'));
    }

    public function testMergeWithNoBinding()
    {
        $h = new Hook;
        $values = array('A', 'B');

        $result = $h->merge('myhook', $values, array('p' => 'c'));
        $this->assertEquals($values, $result);
    }

    public function testMergeWithBindings()
    {
        $h = new Hook;
        $values = array('A', 'B');
        $expected = array('A', 'B', 'c', 'D');

        $h->on('myhook', function ($p) {
            return array($p);
        });

        $h->on('myhook', function () {
            return array('D');
        });

        $result = $h->merge('myhook', $values, array('p' => 'c'));
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected, $values);
    }

    public function testMergeWithBindingButReturningBadData()
    {
        $h = new Hook;
        $values = array('A', 'B');
        $expected = array('A', 'B');

        $h->on('myhook', function () {
            return 'string';
        });

        $result = $h->merge('myhook', $values);
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected, $values);
    }

    public function testFirstWithNoBinding()
    {
        $h = new Hook;

        $result = $h->first('myhook', array('p' => 2));
        $this->assertEquals(null, $result);
    }

    public function testFirstWithMultipleBindings()
    {
        $h = new Hook;

        $h->on('myhook', function ($p) {
            return $p + 1;
        });

        $h->on('myhook', function ($p) {
            return $p;
        });

        $result = $h->first('myhook', array('p' => 3));
        $this->assertEquals(4, $result);
    }
}
