<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Config;

class ConfigTest extends Base
{
    public function testGetTimezones()
    {
        $configModel = new Config($this->container);
        $this->assertNotEmpty($configModel->getTimezones());
        $this->assertArrayHasKey('Europe/Paris', $configModel->getTimezones());
        $this->assertContains('Europe/Paris', $configModel->getTimezones());
        $this->assertArrayNotHasKey('', $configModel->getTimezones());

        $this->assertArrayHasKey('', $configModel->getTimezones(true));
        $this->assertContains('Application default', $configModel->getTimezones(true));
    }

    public function testGetLanguages()
    {
        $configModel = new Config($this->container);
        $this->assertNotEmpty($configModel->getLanguages());
        $this->assertArrayHasKey('fr_FR', $configModel->getLanguages());
        $this->assertContains('Français', $configModel->getLanguages());
        $this->assertArrayNotHasKey('', $configModel->getLanguages());

        $this->assertArrayHasKey('', $configModel->getLanguages(true));
        $this->assertContains('Application default', $configModel->getLanguages(true));
    }

    public function testGetJsLanguage()
    {
        $configModel = new Config($this->container);
        $this->assertEquals('en', $configModel->getJsLanguageCode());

        $this->container['sessionStorage']->user = array('language' => 'fr_FR');
        $this->assertEquals('fr', $configModel->getJsLanguageCode());

        $this->container['sessionStorage']->user = array('language' => 'xx_XX');
        $this->assertEquals('en', $configModel->getJsLanguageCode());
    }

    public function testGetCurrentLanguage()
    {
        $configModel = new Config($this->container);
        $this->assertEquals('en_US', $configModel->getCurrentLanguage());

        $this->container['sessionStorage']->user = array('language' => 'fr_FR');
        $this->assertEquals('fr_FR', $configModel->getCurrentLanguage());

        $this->container['sessionStorage']->user = array('language' => 'xx_XX');
        $this->assertEquals('xx_XX', $configModel->getCurrentLanguage());
    }

    public function testGetCurrentTimezone()
    {
        $configModel = new Config($this->container);
        $this->assertEquals('UTC', $configModel->getCurrentTimezone());

        $this->container['sessionStorage']->user = array('timezone' => 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $configModel->getCurrentTimezone());

        $this->container['sessionStorage']->user = array('timezone' => 'Something');
        $this->assertEquals('Something', $configModel->getCurrentTimezone());
    }

    public function testRegenerateToken()
    {
        $configModel = new Config($this->container);
        $token = $configModel->getOption('api_token');
        $this->assertTrue($configModel->regenerateToken('api_token'));
        $this->assertNotEquals($token, $configModel->getOption('api_token'));
    }

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
        $this->assertEquals('http://localhost/', $c->getOption('application_url'));

        $this->assertTrue($c->save(array('application_url' => 'http://localhost')));
        $this->assertEquals('http://localhost/', $c->getOption('application_url'));

        $this->assertTrue($c->save(array('application_url' => '')));
        $this->assertEquals('', $c->getOption('application_url'));
    }

    public function testDefaultValues()
    {
        $c = new Config($this->container);

        $this->assertEquals(172800, $c->getOption('board_highlight_period'));
        $this->assertEquals(60, $c->getOption('board_public_refresh_interval'));
        $this->assertEquals(10, $c->getOption('board_private_refresh_interval'));
        $this->assertEmpty($c->getOption('board_columns'));

        $this->assertEquals('yellow', $c->getOption('default_color'));
        $this->assertEquals('en_US', $c->getOption('application_language'));
        $this->assertEquals('UTC', $c->getOption('application_timezone'));
        $this->assertEquals('m/d/Y', $c->getOption('application_date_format'));
        $this->assertEmpty($c->getOption('application_url'));
        $this->assertEmpty($c->getOption('application_stylesheet'));
        $this->assertEquals('USD', $c->getOption('application_currency'));

        $this->assertEquals(0, $c->getOption('calendar_user_subtasks_time_tracking'));
        $this->assertEquals('date_started', $c->getOption('calendar_user_tasks'));
        $this->assertEquals('date_started', $c->getOption('calendar_user_tasks'));

        $this->assertEquals(0, $c->getOption('integration_gravatar'));
        $this->assertEquals(1, $c->getOption('cfd_include_closed_tasks'));
        $this->assertEquals(1, $c->getOption('password_reset'));

        $this->assertEquals(1, $c->getOption('subtask_time_tracking'));
        $this->assertEquals(0, $c->getOption('subtask_restriction'));
        $this->assertEmpty($c->getOption('project_categories'));

        $this->assertEmpty($c->getOption('webhook_url_task_modification'));
        $this->assertEmpty($c->getOption('webhook_url_task_creation'));
        $this->assertNotEmpty($c->getOption('webhook_token'));
        $this->assertEmpty($c->getOption('webhook_url'));

        $this->assertNotEmpty($c->getOption('api_token'));
    }

    public function testGetOption()
    {
        $c = new Config($this->container);

        $this->assertEquals('', $c->getOption('board_columns'));
        $this->assertEquals('test', $c->getOption('board_columns', 'test'));
        $this->assertEquals(0, $c->getOption('board_columns', 0));
    }

    public function testGetWithCaching()
    {
        $c = new Config($this->container);

        $this->assertEquals('UTC', $c->get('application_timezone'));
        $this->assertTrue($c->save(array('application_timezone' => 'Europe/Paris')));

        $this->assertEquals('UTC', $c->get('application_timezone')); // cached value
        $this->assertEquals('Europe/Paris', $c->getOption('application_timezone'));

        $this->assertEquals('', $c->get('board_columns'));
        $this->assertEquals('test', $c->get('board_columns', 'test'));
        $this->assertEquals('test', $c->get('empty_value', 'test'));
    }
}
