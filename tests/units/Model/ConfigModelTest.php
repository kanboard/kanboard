<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ConfigModel;

class ConfigModelTest extends Base
{
    public function testRegenerateToken()
    {
        $configModel = new ConfigModel($this->container);
        $token = $configModel->getOption('api_token');
        $this->assertTrue($configModel->regenerateToken('api_token'));
        $this->assertNotEquals($token, $configModel->getOption('api_token'));
    }

    public function testCRUDOperations()
    {
        $configModel = new ConfigModel($this->container);

        $this->assertTrue($configModel->save(array('key1' => 'value1')));
        $this->assertTrue($configModel->save(array('key1' => 'value2')));
        $this->assertTrue($configModel->save(array('key2' => 'value2')));

        $this->assertEquals('value2', $configModel->getOption('key1'));
        $this->assertEquals('value2', $configModel->getOption('key2'));
        $this->assertEquals('', $configModel->getOption('key3'));
        $this->assertEquals('default', $configModel->getOption('key3', 'default'));

        $this->assertTrue($configModel->exists('key1'));
        $this->assertFalse($configModel->exists('key3'));

        $this->assertTrue($configModel->save(array('key1' => 'value1')));

        $this->assertArrayHasKey('key1', $configModel->getAll());
        $this->assertArrayHasKey('key2', $configModel->getAll());

        $this->assertContains('value1', $configModel->getAll());
        $this->assertContains('value2', $configModel->getAll());
    }

    public function testSaveApplicationUrl()
    {
        $configModel = new ConfigModel($this->container);

        $this->assertTrue($configModel->save(array('application_url' => 'http://localhost/')));
        $this->assertEquals('http://localhost/', $configModel->getOption('application_url'));

        $this->assertTrue($configModel->save(array('application_url' => 'http://localhost')));
        $this->assertEquals('http://localhost/', $configModel->getOption('application_url'));

        $this->assertTrue($configModel->save(array('application_url' => '')));
        $this->assertEquals('', $configModel->getOption('application_url'));
    }

    public function testDefaultValues()
    {
        $configModel = new ConfigModel($this->container);

        $this->assertEquals(172800, $configModel->getOption('board_highlight_period'));
        $this->assertEquals(60, $configModel->getOption('board_public_refresh_interval'));
        $this->assertEquals(10, $configModel->getOption('board_private_refresh_interval'));
        $this->assertEmpty($configModel->getOption('board_columns'));

        $this->assertEquals('yellow', $configModel->getOption('default_color'));
        $this->assertEquals('en_US', $configModel->getOption('application_language'));
        $this->assertEquals('UTC', $configModel->getOption('application_timezone'));
        $this->assertEquals('m/d/Y', $configModel->getOption('application_date_format'));
        $this->assertEmpty($configModel->getOption('application_url'));
        $this->assertEmpty($configModel->getOption('application_stylesheet'));
        $this->assertEquals('USD', $configModel->getOption('application_currency'));

        $this->assertEquals(0, $configModel->getOption('calendar_user_subtasks_time_tracking'));
        $this->assertEquals('date_started', $configModel->getOption('calendar_user_tasks'));
        $this->assertEquals('date_started', $configModel->getOption('calendar_user_tasks'));

        $this->assertEquals(0, $configModel->getOption('integration_gravatar'));
        $this->assertEquals(1, $configModel->getOption('cfd_include_closed_tasks'));
        $this->assertEquals(1, $configModel->getOption('password_reset'));

        $this->assertEquals(1, $configModel->getOption('subtask_time_tracking'));
        $this->assertEquals(0, $configModel->getOption('subtask_restriction'));
        $this->assertEmpty($configModel->getOption('project_categories'));

        $this->assertEmpty($configModel->getOption('webhook_url_task_modification'));
        $this->assertEmpty($configModel->getOption('webhook_url_task_creation'));
        $this->assertNotEmpty($configModel->getOption('webhook_token'));
        $this->assertEmpty($configModel->getOption('webhook_url'));

        $this->assertNotEmpty($configModel->getOption('api_token'));
    }

    public function testGetOption()
    {
        $configModel = new ConfigModel($this->container);

        $this->assertEquals('', $configModel->getOption('board_columns'));
        $this->assertEquals('test', $configModel->getOption('board_columns', 'test'));
        $this->assertEquals(0, $configModel->getOption('board_columns', 0));
    }

    public function testGetWithCaching()
    {
        $configModel = new ConfigModel($this->container);

        $this->assertEquals('UTC', $configModel->get('application_timezone'));
        $this->assertTrue($configModel->save(array('application_timezone' => 'Europe/Paris')));

        $this->assertEquals('UTC', $configModel->get('application_timezone')); // cached value
        $this->assertEquals('Europe/Paris', $configModel->getOption('application_timezone'));

        $this->assertEquals('', $configModel->get('board_columns'));
        $this->assertEquals('test', $configModel->get('board_columns', 'test'));
        $this->assertEquals('test', $configModel->get('empty_value', 'test'));
    }

    public function testValueLength()
    {
        $configModel = new ConfigModel($this->container);
        $string = str_repeat('a', 65535);

        $this->assertTrue($configModel->save(array('application_stylesheet' => $string)));
        $this->assertSame($string, $configModel->getOption('application_stylesheet'));
    }
}
