<?php

require_once __DIR__.'/Base.php';

use Model\Config;

class ConfigTest extends Base
{
    public function testDefaultValues()
    {
        $c = new Config($this->registry);

        $this->assertEquals('en_US', $c->get('application_language'));
        $this->assertEquals('UTC', $c->get('application_timezone'));

        $this->assertEmpty($c->get('webhook_url_task_modification'));
        $this->assertEmpty($c->get('webhook_url_task_creation'));
        $this->assertEmpty($c->get('board_columns'));

        $this->assertNotEmpty($c->get('webhook_token'));
        $this->assertNotEmpty($c->get('api_token'));
    }

    public function testGet()
    {
        $c = new Config($this->registry);

        $this->assertEquals('', $c->get('board_columns'));
        $this->assertEquals('test', $c->get('board_columns', 'test'));
        $this->assertEquals(0, $c->get('board_columns', 0));
    }
}
