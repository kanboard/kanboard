<?php

require_once __DIR__.'/../../vendor/autoload.php';

abstract class Base extends PHPUnit_Framework_TestCase
{
    protected $app = null;
    protected $admin = null;
    protected $user = null;

    public static function setUpBeforeClass()
    {
        if (DB_DRIVER === 'sqlite') {
            @unlink(DB_FILENAME);
        } elseif (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        } elseif (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        }

        $service = new Kanboard\ServiceProvider\DatabaseProvider;

        $db = $service->getInstance();
        $db->table('settings')->eq('option', 'api_token')->update(array('value' => API_KEY));
        $db->closeConnection();
    }

    public function setUp()
    {
        $this->app = new JsonRPC\Client(API_URL);
        $this->app->authentication('jsonrpc', API_KEY);
        $this->app->getHttpClient()->withDebug();

        $this->admin = new JsonRPC\Client(API_URL);
        $this->admin->authentication('admin', 'admin');
        $this->admin->getHttpClient()->withDebug();

        $this->user = new JsonRPC\Client(API_URL);
        $this->user->authentication('user', 'password');
        $this->user->getHttpClient()->withDebug();
    }

    protected function getProjectId()
    {
        $projects = $this->app->getAllProjects();
        $this->assertNotEmpty($projects);
        return $projects[0]['id'];
    }

    protected function getGroupId()
    {
        $groups = $this->app->getAllGroups();
        $this->assertNotEmpty($groups);
        return $groups[0]['id'];
    }
}
