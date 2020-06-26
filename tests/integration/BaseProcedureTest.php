<?php

require_once __DIR__.'/../../vendor/autoload.php';

abstract class BaseProcedureTest extends PHPUnit\Framework\TestCase
{
    protected $app = null;
    protected $admin = null;
    protected $manager = null;
    protected $user = null;

    protected $adminUserId = 0;
    protected $managerUserId = 0;
    protected $userUserId = 0;

    protected $projectName = '';
    protected $projectId = 0;
    protected $taskTitle = 'My task';
    protected $taskId = 0;
    protected $subtaskId = 0;

    protected $groupName1 = 'My Group A';
    protected $groupName2 = 'My Group B';
    protected $groupId1;
    protected $groupId2;

    protected $username = 'test-user';
    protected $userId;

    public function setUp()
    {
        $this->setUpAppClient();
        $this->setUpAdminUser();
        $this->setUpManagerUser();
        $this->setUpStandardUser();
    }

    public function setUpAppClient()
    {
        $this->app = new JsonRPC\Client(API_URL);
        $this->app->authentication('jsonrpc', API_KEY);
        $this->app->getHttpClient()->withDebug()->withTimeout(10);
    }

    public function setUpAdminUser()
    {
        $this->adminUserId = $this->getUserId('superuser');

        if (! $this->adminUserId) {
            $this->adminUserId = $this->app->createUser('superuser', 'password', 'Admin User', 'user@localhost', 'app-admin');
            $this->assertNotFalse($this->adminUserId);
        }

        $this->admin = new JsonRPC\Client(API_URL);
        $this->admin->authentication('superuser', 'password');
        $this->admin->getHttpClient()->withDebug();
    }

    public function setUpManagerUser()
    {
        $this->managerUserId = $this->getUserId('manager');

        if (! $this->managerUserId) {
            $this->managerUserId = $this->app->createUser('manager', 'password', 'Manager User', 'user@localhost', 'app-manager');
            $this->assertNotFalse($this->managerUserId);
        }

        $this->manager = new JsonRPC\Client(API_URL);
        $this->manager->authentication('manager', 'password');
        $this->manager->getHttpClient()->withDebug();
    }

    public function setUpStandardUser()
    {
        $this->userUserId = $this->getUserId('user');

        if (! $this->userUserId) {
            $this->userUserId = $this->app->createUser('user', 'password', 'Standard User', 'user@localhost', 'app-user');
            $this->assertNotFalse($this->userUserId);
        }

        $this->user = new JsonRPC\Client(API_URL);
        $this->user->authentication('user', 'password');
        $this->user->getHttpClient()->withDebug();
    }

    public function getUserId($username)
    {
        $user = $this->app->getUserByName($username);

        if (! empty($user)) {
            return $user['id'];
        }

        return 0;
    }

    public function assertCreateTeamProject()
    {
        $this->projectId = $this->app->createProject($this->projectName, 'Description');
        $this->assertNotFalse($this->projectId);
    }

    public function assertCreateUser()
    {
        $this->userId = $this->app->createUser($this->username, 'password');
        $this->assertNotFalse($this->userId);
    }

    public function assertCreateGroups()
    {
        $this->groupId1 = $this->app->createGroup($this->groupName1);
        $this->groupId2 = $this->app->createGroup($this->groupName2, 'External ID');
        $this->assertNotFalse($this->groupId1);
        $this->assertNotFalse($this->groupId2);
    }

    public function assertCreateTask()
    {
        $this->taskId = $this->app->createTask(array('title' => $this->taskTitle, 'project_id' => $this->projectId));
        $this->assertNotFalse($this->taskId);
    }

    public function assertCreateSubtask()
    {
        $this->subtaskId = $this->app->createSubtask(array(
            'task_id' => $this->taskId,
            'title' => 'subtask #1',
        ));

        $this->assertNotFalse($this->subtaskId);
    }
}
