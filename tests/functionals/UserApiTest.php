<?php

require_once __DIR__.'/../../vendor/autoload.php';

class UserApi extends PHPUnit_Framework_TestCase
{
    private $app = null;
    private $admin = null;
    private $user = null;

    public static function setUpBeforeClass()
    {
        if (DB_DRIVER === 'sqlite') {
            @unlink(DB_FILENAME);
        }
        else if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        }
        else if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        }

        $service = new ServiceProvider\DatabaseProvider;

        $db = $service->getInstance();
        $db->table('settings')->eq('option', 'api_token')->update(array('value' => API_KEY));
        $db->closeConnection();
    }

    public function setUp()
    {
        $this->app = new JsonRPC\Client(API_URL);
        $this->app->authentication('jsonrpc', API_KEY);
        $this->app->debug = true;

        $this->admin = new JsonRPC\Client(API_URL);
        $this->admin->authentication('admin', 'admin');
        $this->admin->debug = true;

        $this->user = new JsonRPC\Client(API_URL);
        $this->user->authentication('user', 'password');
        $this->user->debug = true;
    }

    public function testCreateProject()
    {
        $this->assertEquals(1, $this->app->createProject('team project'));
    }

    public function testCreateUser()
    {
        $this->assertEquals(2, $this->app->createUser('user', 'password'));
    }

    /**
     * @expectedException JsonRPC\AccessDeniedException
     */
    public function testNotAllowedAppProcedure()
    {
        $this->app->getMe();
    }

    /**
     * @expectedException JsonRPC\AccessDeniedException
     */
    public function testNotAllowedUserProcedure()
    {
        $this->user->getAllProjects();
    }

    /**
     * @expectedException JsonRPC\AccessDeniedException
     */
    public function testNotAllowedProjectForUser()
    {
        $this->user->getProjectById(1);
    }

    public function testAllowedProjectForAdmin()
    {
        $this->assertNotEmpty($this->admin->getProjectById(1));
    }

    public function testGetTimezone()
    {
        $this->assertEquals('UTC', $this->user->getTimezone());
    }

    public function testGetVersion()
    {
        $this->assertEquals('master', $this->user->getVersion());
    }

    public function testGetMe()
    {
        $profile = $this->user->getMe();
        $this->assertNotEmpty($profile);
        $this->assertEquals('user', $profile['username']);
    }

    public function testCreateMyPrivateProject()
    {
        $this->assertEquals(2, $this->user->createMyPrivateProject('my project'));
    }

    public function testGetMyProjectsList()
    {
        $projects = $this->user->getMyProjectsList();
        $this->assertNotEmpty($projects);
        $this->assertArrayNotHasKey(1, $projects);
        $this->assertArrayHasKey(2, $projects);
        $this->assertEquals('my project', $projects[2]);
    }

    public function testGetProjectById()
    {
        $project = $this->user->getProjectById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('my project', $project['name']);
        $this->assertEquals(1, $project['is_private']);
    }

    public function testCreateTask()
    {
        $this->assertEquals(1, $this->user->createTask('my user title', 2));
        $this->assertEquals(2, $this->admin->createTask('my admin title', 1));
    }

    public function testGetTask()
    {
        $task = $this->user->getTask(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('my user title', $task['title']);
    }

    /**
     * @expectedException JsonRPC\AccessDeniedException
     */
    public function testGetAdminTask()
    {
        $this->user->getTask(2);
    }

    public function testGetMyActivityStream()
    {
        $activity = $this->user->getMyActivityStream();
        $this->assertNotEmpty($activity);
    }

    public function testCloseTask()
    {
        $this->assertTrue($this->user->closeTask(1));
    }

    public function testOpenTask()
    {
        $this->assertTrue($this->user->openTask(1));
    }

    public function testMoveTaskPosition()
    {
        $this->assertTrue($this->user->moveTaskPosition(2, 1, 2, 1));
    }

    public function testUpdateTask()
    {
        $this->assertTrue($this->user->updateTask(array('id' => 1, 'title' => 'new title', 'reference' => 'test', 'owner_id' => 2)));
    }

    public function testGetbyReference()
    {
        $task = $this->user->getTaskByReference(2, 'test');
        $this->assertNotEmpty($task);
        $this->assertEquals('new title', $task['title']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testGetMyDashboard()
    {
        $dashboard = $this->user->getMyDashboard();
        $this->assertNotEmpty($dashboard);
        $this->assertArrayHasKey('projects', $dashboard);
        $this->assertArrayHasKey('tasks', $dashboard);
        $this->assertArrayHasKey('subtasks', $dashboard);
        $this->assertNotEmpty($dashboard['projects']);
        $this->assertNotEmpty($dashboard['tasks']);
    }

    public function testGetBoard()
    {
        $this->assertNotEmpty($this->user->getBoard(2));
    }
}
