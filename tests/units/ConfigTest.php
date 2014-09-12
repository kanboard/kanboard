<?php

require_once __DIR__.'/Base.php';

use Model\Config;

class ConfigTest extends Base
{
    public function testDefaultValues()
    {
        $c = new Config($this->registry);

        $this->assertEquals('en_US', $c->get('language'));
        $this->assertEquals('UTC', $c->get('timezone'));

        $this->assertEmpty($c->get('webhooks_url_task_modification'));
        $this->assertEmpty($c->get('webhooks_url_task_creation'));
        $this->assertEmpty($c->get('default_columns'));

        $this->assertNotEmpty($c->get('webhooks_token'));
        $this->assertNotEmpty($c->get('api_token'));
    }

    public function testGet()
    {
        $c = new Config($this->registry);

        $this->assertEquals('', $c->get('default_columns'));
        $this->assertEquals('test', $c->get('default_columns', 'test'));
        $this->assertEquals(0, $c->get('default_columns', 0));
    }
}
