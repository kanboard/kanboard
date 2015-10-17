<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Config;
use Kanboard\Core\Session;

class ConfigTest extends Base
{
    public function testCRUDOperations()
    {
        $c = new Config($this->container);

        $this->assertTrue($c->save(array('key1' => 'value1')));
        $this->assertTrue($c->save(array('key1' => 'value2')));
        $this->assertTrue($c->save(array('key2' => 'value2')));

        $this->assertEquals('value2', $c->getOption('key1'));
        $this->assertEquals('value2', $c->getOption('key2'));
        $this->assertEquals('', $c->getOption('key3'));
        $this->assertEquals('default', $c->getOption('key3', 'default'));

        $this->assertTrue($c->exists('key1'));
        $this->assertFalse($c->exists('key3'));

        $this->assertTrue($c->save(array('key1' => 'value1')));

        $this->assertArrayHasKey('key1', $c->getAll());
        $this->assertArrayHasKey('key2', $c->getAll());

        $this->assertContains('value1', $c->getAll());
        $this->assertContains('value2', $c->getAll());
    }

    public function testSaveApplicationUrl()
    {
        $c = new Config($this->container);

        $this->assertTrue($c->save(array('application_url' => 'http://localhost/')));
        $this->assertEquals('http://localhost/', $c->get('application_url'));

        $this->assertTrue($c->save(array('application_url' => 'http://localhost')));
        $this->assertEquals('http://localhost/', $c->get('application_url'));

        $this->assertTrue($c->save(array('application_url' => '')));
        $this->assertEquals('', $c->get('application_url'));
    }

    public function testDefaultValues()
    {
        $c = new Config($this->container);

        $this->assertEquals('en_US', $c->get('application_language'));
        $this->assertEquals('UTC', $c->get('application_timezone'));

        $this->assertEmpty($c->get('webhook_url_task_modification'));
        $this->assertEmpty($c->get('webhook_url_task_creation'));
        $this->assertEmpty($c->get('board_columns'));
        $this->assertEmpty($c->get('application_url'));

        $this->assertNotEmpty($c->get('webhook_token'));
        $this->assertNotEmpty($c->get('api_token'));
    }

    public function testGet()
    {
        $c = new Config($this->container);

        $this->assertEquals('', $c->get('board_columns'));
        $this->assertEquals('test', $c->get('board_columns', 'test'));
        $this->assertEquals(0, $c->get('board_columns', 0));
    }

    public function testGetWithSession()
    {
        $this->container['session'] = new Session;
        $c = new Config($this->container);

        session_id('test');

        $this->assertTrue(Session::isOpen());

        $this->assertEquals('', $c->get('board_columns'));
        $this->assertEquals('test', $c->get('board_columns', 'test'));

        $this->container['session']['config'] = array(
            'board_columns' => 'foo',
            'empty_value' => 0
        );

        $this->assertEquals('foo', $c->get('board_columns'));
        $this->assertEquals('foo', $c->get('board_columns', 'test'));
        $this->assertEquals('test', $c->get('empty_value', 'test'));

        session_id('');
        unset($this->container['session']);
    }
}
