<?php

require_once __DIR__.'/../../vendor/autoload.php';

class Api extends PHPUnit_Framework_TestCase
{
    private $client = null;

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
        $db->table('settings')->eq('option', 'application_timezone')->update(array('value' => 'Europe/Paris'));
        $db->closeConnection();
    }

    public function setUp()
    {
        $this->client = new JsonRPC\Client(API_URL);
        $this->client->authentication('jsonrpc', API_KEY);
        // $this->client->debug = true;
    }

    private function getTaskId()
    {
        $tasks = $this->client->getAllTasks(1, 1);
        $this->assertNotEmpty($tasks);

        return $tasks[0]['id'];
    }

    public function testGetTimezone()
    {
        $this->assertEquals('Europe/Paris', $this->client->getTimezone());
    }

    public function testGetVersion()
    {
        $this->assertEquals('master', $this->client->getVersion());
    }

    public function testRemoveAll()
    {
        $projects = $this->client->getAllProjects();

        if ($projects) {
            foreach ($projects as $project) {
                $this->assertEquals('http://127.0.0.1:8000/?controller=board&action=show&project_id='.$project['id'], $project['url']['board']);
                $this->assertEquals('http://127.0.0.1:8000/?controller=calendar&action=show&project_id='.$project['id'], $project['url']['calendar']);
                $this->assertEquals('http://127.0.0.1:8000/?controller=listing&action=show&project_id='.$project['id'], $project['url']['list']);
                $this->assertTrue($this->client->removeProject($project['id']));
            }
        }
    }

    public function testCreateProject()
    {
        $project_id = $this->client->createProject('API test');
        $this->assertNotFalse($project_id);
        $this->assertInternalType('int', $project_id);
    }

    public function testGetProjectById()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=board&action=show&project_id='.$project['id'], $project['url']['board']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=calendar&action=show&project_id='.$project['id'], $project['url']['calendar']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=listing&action=show&project_id='.$project['id'], $project['url']['list']);
    }

    public function testGetProjectByName()
    {
        $project = $this->client->getProjectByName('API test');
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=board&action=show&project_id='.$project['id'], $project['url']['board']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=calendar&action=show&project_id='.$project['id'], $project['url']['calendar']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=listing&action=show&project_id='.$project['id'], $project['url']['list']);

        $project = $this->client->getProjectByName(array('name' => 'API test'));
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);

        $project = $this->client->getProjectByName('None');
        $this->assertEmpty($project);
        $this->assertNull($project);
    }

    public function testGetAllProjects()
    {
        $projects = $this->client->getAllProjects();
        $this->assertNotEmpty($projects);

        foreach ($projects as $project) {
            $this->assertEquals('http://127.0.0.1:8000/?controller=board&action=show&project_id='.$project['id'], $project['url']['board']);
            $this->assertEquals('http://127.0.0.1:8000/?controller=calendar&action=show&project_id='.$project['id'], $project['url']['calendar']);
            $this->assertEquals('http://127.0.0.1:8000/?controller=listing&action=show&project_id='.$project['id'], $project['url']['list']);
        }
    }

    public function testUpdateProject()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($this->client->execute('updateProject', array('id' => 1, 'name' => 'API test 2')));

        $project = $this->client->getProjectById(1);
        $this->assertEquals('API test 2', $project['name']);

        $this->assertTrue($this->client->execute('updateProject', array('id' => 1, 'name' => 'API test', 'description' => 'test')));

        $project = $this->client->getProjectById(1);
        $this->assertEquals('API test', $project['name']);
        $this->assertEquals('test', $project['description']);
    }

    public function testDisableProject()
    {
        $this->assertTrue($this->client->disableProject(1));
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_active']);
    }

    public function testEnableProject()
    {
        $this->assertTrue($this->client->enableProject(1));
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);
    }

    public function testEnableProjectPublicAccess()
    {
        $this->assertTrue($this->client->enableProjectPublicAccess(1));
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_public']);
        $this->assertNotEmpty($project['token']);
    }

    public function testDisableProjectPublicAccess()
    {
        $this->assertTrue($this->client->disableProjectPublicAccess(1));
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);
    }

    public function testgetProjectActivities()
    {
        $activities = $this->client->getProjectActivities(array('project_ids' => array(1)));
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function testgetProjectActivity()
    {
        $activities = $this->client->getProjectActivity(1);
        $this->assertInternalType('array', $activities);
        $this->assertCount(0, $activities);
    }

    public function testGetBoard()
    {
        $board = $this->client->getBoard(1);
        $this->assertTrue(is_array($board));
        $this->assertEquals(1, count($board));
        $this->assertEquals('Default swimlane', $board[0]['name']);
        $this->assertEquals(4, count($board[0]['columns']));
    }

    public function testGetColumns()
    {
        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(4, count($columns));
        $this->assertEquals('Done', $columns[3]['title']);
    }

    public function testMoveColumnUp()
    {
        $this->assertTrue($this->client->moveColumnUp(1, 4));

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals('Done', $columns[2]['title']);
        $this->assertEquals('Work in progress', $columns[3]['title']);
    }

    public function testMoveColumnDown()
    {
        $this->assertTrue($this->client->moveColumnDown(1, 4));

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals('Work in progress', $columns[2]['title']);
        $this->assertEquals('Done', $columns[3]['title']);
    }

    public function testUpdateColumn()
    {
        $this->assertTrue($this->client->updateColumn(4, 'Boo', 2));

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals('Boo', $columns[3]['title']);
        $this->assertEquals(2, $columns[3]['task_limit']);
    }

    public function testAddColumn()
    {
        $column_id = $this->client->addColumn(1, 'New column');

        $this->assertNotFalse($column_id);
        $this->assertInternalType('int', $column_id);
        $this->assertTrue($column_id > 0);

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(5, count($columns));
        $this->assertEquals('New column', $columns[4]['title']);
    }

    public function testRemoveColumn()
    {
        $this->assertTrue($this->client->removeColumn(5));

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals(4, count($columns));
    }

    public function testGetDefaultSwimlane()
    {
        $swimlane = $this->client->getDefaultSwimlane(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Default swimlane', $swimlane['default_swimlane']);
    }

    public function testAddSwimlane()
    {
        $swimlane_id = $this->client->addSwimlane(1, 'Swimlane 1');
        $this->assertNotFalse($swimlane_id);
        $this->assertInternalType('int', $swimlane_id);

        $swimlane = $this->client->getSwimlaneById($swimlane_id);
        $this->assertNotEmpty($swimlane);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals('Swimlane 1', $swimlane['name']);
    }

    public function testGetSwimlane()
    {
        $swimlane = $this->client->getSwimlane(1);
        $this->assertNotEmpty($swimlane);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals('Swimlane 1', $swimlane['name']);
    }

    public function testUpdateSwimlane()
    {
        $swimlane = $this->client->getSwimlaneByName(1, 'Swimlane 1');
        $this->assertNotEmpty($swimlane);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals(1, $swimlane['id']);
        $this->assertEquals('Swimlane 1', $swimlane['name']);

        $this->assertTrue($this->client->updateSwimlane($swimlane['id'], 'Another swimlane'));

        $swimlane = $this->client->getSwimlaneById($swimlane['id']);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Another swimlane', $swimlane['name']);
    }

    public function testDisableSwimlane()
    {
        $this->assertTrue($this->client->disableSwimlane(1, 1));

        $swimlane = $this->client->getSwimlaneById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(0, $swimlane['is_active']);
    }

    public function testEnableSwimlane()
    {
        $this->assertTrue($this->client->enableSwimlane(1, 1));

        $swimlane = $this->client->getSwimlaneById(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals(1, $swimlane['is_active']);
    }

    public function testGetAllSwimlanes()
    {
        $this->assertNotFalse($this->client->addSwimlane(1, 'Swimlane A'));

        $swimlanes = $this->client->getAllSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Another swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[1]['name']);
    }

    public function testGetActiveSwimlane()
    {
        $this->assertTrue($this->client->disableSwimlane(1, 1));

        $swimlanes = $this->client->getActiveSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[1]['name']);
    }

    public function testMoveSwimlaneUp()
    {
        $this->assertTrue($this->client->enableSwimlane(1, 1));
        $this->assertTrue($this->client->moveSwimlaneUp(1, 1));

        $swimlanes = $this->client->getActiveSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertCount(3, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Another swimlane', $swimlanes[1]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[2]['name']);

        $this->assertTrue($this->client->moveSwimlaneUp(1, 2));

        $swimlanes = $this->client->getActiveSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertCount(3, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[1]['name']);
        $this->assertEquals('Another swimlane', $swimlanes[2]['name']);
    }

    public function testMoveSwimlaneDown()
    {
        $this->assertTrue($this->client->moveSwimlaneDown(1, 2));

        $swimlanes = $this->client->getActiveSwimlanes(1);
        $this->assertNotEmpty($swimlanes);
        $this->assertCount(3, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Another swimlane', $swimlanes[1]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[2]['name']);
    }

    public function testCreateTask()
    {
        $task = array(
            'title' => 'Task #1',
            'color_id' => 'blue',
            'owner_id' => 1,
            'project_id' => 1,
            'column_id' => 2,
        );

        $task_id = $this->client->createTask($task);

        $this->assertNotFalse($task_id);
        $this->assertInternalType('int', $task_id);
        $this->assertTrue($task_id > 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateTaskWithBadParams()
    {
        $task = array(
            'title' => 'Task #1',
            'color_id' => 'blue',
            'owner_id' => 1,
        );

        $this->client->createTask($task);
    }

    public function testGetTask()
    {
        $task = $this->client->getTask(1);

        $this->assertNotFalse($task);
        $this->assertTrue(is_array($task));
        $this->assertEquals('Task #1', $task['title']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'], $task['url']);
    }

    public function testGetAllTasks()
    {
        $tasks = $this->client->getAllTasks(1, 1);

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEquals('Task #1', $tasks[0]['title']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=task&action=show&task_id='.$tasks[0]['id'].'&project_id='.$tasks[0]['project_id'], $tasks[0]['url']);

        $tasks = $this->client->getAllTasks(2, 0);

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEmpty($tasks);
    }

    public function testMoveTaskSwimlane()
    {
        $task_id = $this->getTaskId();

        $task = $this->client->getTask($task_id);
        $this->assertNotFalse($task);
        $this->assertTrue(is_array($task));
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(0, $task['swimlane_id']);

        $moved_timestamp = $task['date_moved'];
        sleep(1);
        $this->assertTrue($this->client->moveTaskPosition(1, $task_id, 4, 1, 2));

        $task = $this->client->getTask($task_id);
        $this->assertNotFalse($task);
        $this->assertTrue(is_array($task));
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertNotEquals($moved_timestamp, $task['date_moved']);
    }

    public function testRemoveSwimlane()
    {
        $this->assertTrue($this->client->removeSwimlane(1, 2));

        $task = $this->client->getTask($this->getTaskId());
        $this->assertNotFalse($task);
        $this->assertTrue(is_array($task));
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(4, $task['column_id']);
        $this->assertEquals(0, $task['swimlane_id']);
    }

    public function testUpdateTask()
    {
        $task = $this->client->getTask(1);

        $values = array();
        $values['id'] = $task['id'];
        $values['color_id'] = 'green';
        $values['description'] = 'test';
        $values['date_due'] = '';

        $this->assertTrue($this->client->execute('updateTask', $values));
    }

    public function testRemoveTask()
    {
        $this->assertTrue($this->client->removeTask(1));
    }

    public function testRemoveUsers()
    {
        $users = $this->client->getAllUsers();
        $this->assertNotFalse($users);
        $this->assertNotEmpty($users);

        foreach ($users as $user) {
            if ($user['id'] > 1) {
                $this->assertTrue($this->client->removeUser($user['id']));
            }
        }
    }

    public function testCreateUser()
    {
        $user = array(
            'username' => 'toto',
            'name' => 'Toto',
            'password' => '123456',
        );

        $user_id = $this->client->execute('createUser', $user);
        $this->assertNotFalse($user_id);
        $this->assertInternalType('int', $user_id);
        $this->assertTrue($user_id > 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateUserWithBadParams()
    {
        $user = array(
            'name' => 'Titi',
            'password' => '123456',
        );

        $this->assertNull($this->client->execute('createUser', $user));
    }

    public function testGetUser()
    {
        $user = $this->client->getUser(2);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('toto', $user['username']);

        $this->assertNull($this->client->getUser(2222));
    }

    public function testUpdateUser()
    {
        $user = array();
        $user['id'] = 2;
        $user['username'] = 'titi';
        $user['name'] = 'Titi';

        $this->assertTrue($this->client->execute('updateUser', $user));

        $user = $this->client->getUser(2);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('titi', $user['username']);
        $this->assertEquals('Titi', $user['name']);

        $user = array();
        $user['id'] = 2;
        $user['email'] = 'titi@localhost';

        $this->assertTrue($this->client->execute('updateUser', $user));

        $user = $this->client->getUser(2);
        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('titi@localhost', $user['email']);
    }

    public function testGetAllowedUsers()
    {
        $users = $this->client->getMembers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(), $users);
    }

    public function testAllowedUser()
    {
        $this->assertTrue($this->client->allowUser(1, 2));

        $users = $this->client->getMembers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(2 => 'Titi'), $users);
    }

    public function testRevokeUser()
    {
        $this->assertTrue($this->client->revokeUser(1, 2));

        $users = $this->client->getMembers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(), $users);
    }

    public function testCreateComment()
    {
        $task = array(
            'title' => 'Task with comment',
            'color_id' => 'red',
            'owner_id' => 1,
            'project_id' => 1,
            'column_id' => 1,
        );

        $this->assertNotFalse($this->client->execute('createTask', $task));

        $tasks = $this->client->getAllTasks(1, 1);
        $this->assertNotEmpty($tasks);
        $this->assertEquals(1, count($tasks));

        $comment = array(
            'task_id' => $tasks[0]['id'],
            'user_id' => 2,
            'content' => 'boo',
        );

        $comment_id = $this->client->execute('createComment', $comment);

        $this->assertNotFalse($comment_id);
        $this->assertInternalType('int', $comment_id);
        $this->assertTrue($comment_id > 0);
    }

    public function testGetComment()
    {
        $comment = $this->client->getComment(1);
        $this->assertNotFalse($comment);
        $this->assertNotEmpty($comment);
        $this->assertEquals(2, $comment['user_id']);
        $this->assertEquals('boo', $comment['comment']);
    }

    public function testUpdateComment()
    {
        $comment = array();
        $comment['id'] = 1;
        $comment['content'] = 'test';

        $this->assertTrue($this->client->execute('updateComment', $comment));

        $comment = $this->client->getComment(1);
        $this->assertEquals('test', $comment['comment']);
    }

    public function testGetAllComments()
    {
        $task_id = $this->getTaskId();

        $comment = array(
            'task_id' => $task_id,
            'user_id' => 1,
            'content' => 'blabla',
        );

        $comment_id = $this->client->createComment($comment);

        $this->assertNotFalse($comment_id);
        $this->assertInternalType('int', $comment_id);
        $this->assertTrue($comment_id > 0);

        $comments = $this->client->getAllComments($task_id);
        $this->assertNotFalse($comments);
        $this->assertNotEmpty($comments);
        $this->assertTrue(is_array($comments));
        $this->assertEquals(2, count($comments));
    }

    public function testRemoveComment()
    {
        $task_id = $this->getTaskId();

        $comments = $this->client->getAllComments($task_id);
        $this->assertNotFalse($comments);
        $this->assertNotEmpty($comments);
        $this->assertTrue(is_array($comments));

        foreach ($comments as $comment) {
            $this->assertTrue($this->client->removeComment($comment['id']));
        }

        $comments = $this->client->getAllComments($task_id);
        $this->assertNotFalse($comments);
        $this->assertEmpty($comments);
        $this->assertTrue(is_array($comments));
    }

    public function testCreateSubtask()
    {
        $subtask = array(
            'task_id' => $this->getTaskId(),
            'title' => 'subtask #1',
        );

        $subtask_id = $this->client->createSubtask($subtask);

        $this->assertNotFalse($subtask_id);
        $this->assertInternalType('int', $subtask_id);
        $this->assertTrue($subtask_id > 0);
    }

    public function testGetSubtask()
    {
        $subtask = $this->client->getSubtask(1);
        $this->assertNotFalse($subtask);
        $this->assertNotEmpty($subtask);
        $this->assertEquals($this->getTaskId(), $subtask['task_id']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
    }

    public function testUpdateSubtask()
    {
        $subtask = array();
        $subtask['id'] = 1;
        $subtask['task_id'] = $this->getTaskId();
        $subtask['title'] = 'test';

        $this->assertTrue($this->client->execute('updateSubtask', $subtask));

        $subtask = $this->client->getSubtask(1);
        $this->assertEquals('test', $subtask['title']);
    }

    public function testGetAllSubtasks()
    {
        $subtask = array(
            'task_id' => $this->getTaskId(),
            'user_id' => 2,
            'title' => 'Subtask #2',
        );

        $this->assertNotFalse($this->client->execute('createSubtask', $subtask));

        $subtasks = $this->client->getAllSubtasks($this->getTaskId());
        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertTrue(is_array($subtasks));
        $this->assertEquals(2, count($subtasks));
    }

    public function testRemoveSubtask()
    {
        $this->assertTrue($this->client->removeSubtask(1));

        $subtasks = $this->client->getAllSubtasks($this->getTaskId());
        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertTrue(is_array($subtasks));
        $this->assertEquals(1, count($subtasks));
    }

    public function testMoveTaskPosition()
    {
        $task_id = $this->getTaskId();
        $this->assertTrue($this->client->moveTaskPosition(1, $task_id, 3, 1));

        $task = $this->client->getTask($task_id);
        $this->assertNotFalse($task);
        $this->assertTrue(is_array($task));
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(3, $task['column_id']);
    }

    public function testCategoryCreation()
    {
        $category = array(
            'name' => 'Category',
            'project_id' => 1,
        );

        $cat_id = $this->client->execute('createCategory', $category);
        $this->assertNotFalse($cat_id);
        $this->assertInternalType('int', $cat_id);
        $this->assertTrue($cat_id > 0);

        // Duplicate

        $category = array(
            'name' => 'Category',
            'project_id' => 1,
        );

        $this->assertFalse($this->client->execute('createCategory', $category));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCategoryCreationWithBadParams()
    {
        // Missing project id
        $category = array(
            'name' => 'Category',
        );

        $this->assertNull($this->client->execute('createCategory', $category));
    }

    public function testCategoryRead()
    {
        $category = $this->client->getCategory(1);

        $this->assertTrue(is_array($category));
        $this->assertNotEmpty($category);
        $this->assertEquals(1, $category['id']);
        $this->assertEquals('Category', $category['name']);
        $this->assertEquals(1, $category['project_id']);
    }

    public function testGetAllCategories()
    {
        $categories = $this->client->getAllCategories(1);

        $this->assertNotEmpty($categories);
        $this->assertNotFalse($categories);
        $this->assertTrue(is_array($categories));
        $this->assertEquals(1, count($categories));
        $this->assertEquals(1, $categories[0]['id']);
        $this->assertEquals('Category', $categories[0]['name']);
        $this->assertEquals(1, $categories[0]['project_id']);
    }

    public function testCategoryUpdate()
    {
        $category = array(
            'id' => 1,
            'name' => 'Renamed category',
        );

        $this->assertTrue($this->client->execute('updateCategory', $category));

        $category = $this->client->getCategory(1);
        $this->assertTrue(is_array($category));
        $this->assertNotEmpty($category);
        $this->assertEquals(1, $category['id']);
        $this->assertEquals('Renamed category', $category['name']);
        $this->assertEquals(1, $category['project_id']);
    }

    public function testCategoryRemove()
    {
        $this->assertTrue($this->client->removeCategory(1));
        $this->assertFalse($this->client->removeCategory(1));
        $this->assertFalse($this->client->removeCategory(1111));
    }

    public function testGetAvailableActions()
    {
        $actions = $this->client->getAvailableActions();
        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('TaskLogMoveAnotherColumn', $actions);
    }

    public function testGetAvailableActionEvents()
    {
        $events = $this->client->getAvailableActionEvents();
        $this->assertNotEmpty($events);
        $this->assertInternalType('array', $events);
        $this->assertArrayHasKey('task.move.column', $events);
    }

    public function testGetCompatibleActionEvents()
    {
        $events = $this->client->getCompatibleActionEvents('TaskClose');
        $this->assertNotEmpty($events);
        $this->assertInternalType('array', $events);
        $this->assertArrayHasKey('task.move.column', $events);
    }

    public function testCreateAction()
    {
        $action_id = $this->client->createAction(1, 'task.move.column', 'TaskClose', array('column_id' => 1));
        $this->assertNotFalse($action_id);
        $this->assertEquals(1, $action_id);
    }

    public function testGetActions()
    {
        $actions = $this->client->getActions(1);
        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertCount(1, $actions);
        $this->assertArrayHasKey('id', $actions[0]);
        $this->assertArrayHasKey('project_id', $actions[0]);
        $this->assertArrayHasKey('event_name', $actions[0]);
        $this->assertArrayHasKey('action_name', $actions[0]);
        $this->assertArrayHasKey('params', $actions[0]);
        $this->assertArrayHasKey('column_id', $actions[0]['params']);
    }

    public function testRemoveAction()
    {
        $this->assertTrue($this->client->removeAction(1));

        $actions = $this->client->getActions(1);
        $this->assertEmpty($actions);
        $this->assertCount(0, $actions);
    }

    public function testGetAllLinks()
    {
        $links = $this->client->getAllLinks();
        $this->assertNotEmpty($links);
        $this->assertArrayHasKey('id', $links[0]);
        $this->assertArrayHasKey('label', $links[0]);
        $this->assertArrayHasKey('opposite_id', $links[0]);
    }

    public function testGetOppositeLink()
    {
        $link = $this->client->getOppositeLinkId(1);
        $this->assertEquals(1, $link);

        $link = $this->client->getOppositeLinkId(2);
        $this->assertEquals(3, $link);
    }

    public function testGetLinkByLabel()
    {
        $link = $this->client->getLinkByLabel('blocks');
        $this->assertNotEmpty($link);
        $this->assertEquals(2, $link['id']);
        $this->assertEquals(3, $link['opposite_id']);
    }

    public function testGetLinkById()
    {
        $link = $this->client->getLinkById(4);
        $this->assertNotEmpty($link);
        $this->assertEquals(4, $link['id']);
        $this->assertEquals(5, $link['opposite_id']);
        $this->assertEquals('duplicates', $link['label']);
    }

    public function testCreateLink()
    {
        $link_id = $this->client->createLink(array('label' => 'test'));
        $this->assertNotFalse($link_id);
        $this->assertInternalType('int', $link_id);

        $link_id = $this->client->createLink(array('label' => 'foo', 'opposite_label' => 'bar'));
        $this->assertNotFalse($link_id);
        $this->assertInternalType('int', $link_id);
    }

    public function testUpdateLink()
    {
        $link1 = $this->client->getLinkByLabel('bar');
        $this->assertNotEmpty($link1);

        $link2 = $this->client->getLinkByLabel('test');
        $this->assertNotEmpty($link2);

        $this->assertNotFalse($this->client->updateLink($link1['id'], $link2['id'], 'boo'));

        $link = $this->client->getLinkById($link1['id']);
        $this->assertNotEmpty($link);
        $this->assertEquals($link2['id'], $link['opposite_id']);
        $this->assertEquals('boo', $link['label']);

        $this->assertTrue($this->client->removeLink($link1['id']));
    }

    public function testCreateTaskLink()
    {
        $task_id1 = $this->client->createTask(array('project_id' => 1, 'title' => 'A'));
        $this->assertNotFalse($task_id1);

        $task_id2 = $this->client->createTask(array('project_id' => 1, 'title' => 'B'));
        $this->assertNotFalse($task_id2);

        $task_id3 = $this->client->createTask(array('project_id' => 1, 'title' => 'C'));
        $this->assertNotFalse($task_id3);

        $task_link_id = $this->client->createTaskLink($task_id1, $task_id2, 1);
        $this->assertNotFalse($task_link_id);

        $task_link = $this->client->getTaskLinkById($task_link_id);
        $this->assertNotEmpty($task_link);
        $this->assertEquals($task_id1, $task_link['task_id']);
        $this->assertEquals($task_id2, $task_link['opposite_task_id']);
        $this->assertEquals(1, $task_link['link_id']);

        $task_links = $this->client->getAllTaskLinks($task_id1);
        $this->assertNotEmpty($task_links);
        $this->assertCount(1, $task_links);

        $this->assertTrue($this->client->updateTaskLink($task_link_id, $task_id1, $task_id3, 2));

        $task_link = $this->client->getTaskLinkById($task_link_id);
        $this->assertNotEmpty($task_link);
        $this->assertEquals($task_id1, $task_link['task_id']);
        $this->assertEquals($task_id3, $task_link['opposite_task_id']);
        $this->assertEquals(2, $task_link['link_id']);

        $this->assertTrue($this->client->removeTaskLink($task_link_id));
        $this->assertEmpty($this->client->getAllTaskLinks($task_id1));
    }

    public function testCreateFile()
    {
        $this->assertNotFalse($this->client->createFile(1, $this->getTaskId(), 'My file', base64_encode('plain text file')));
    }

    public function testGetAllFiles()
    {
        $files = $this->client->getAllFiles(array('task_id' => $this->getTaskId()));

        $this->assertNotEmpty($files);
        $this->assertCount(1, $files);
        $this->assertEquals('My file', $files[0]['name']);

        $file = $this->client->getFile($files[0]['id']);
        $this->assertNotEmpty($file);
        $this->assertEquals('My file', $file['name']);

        $content = $this->client->downloadFile($file['id']);
        $this->assertNotEmpty($content);
        $this->assertEquals('plain text file', base64_decode($content));

        $content = $this->client->downloadFile(1234567);
        $this->assertEmpty($content);

        $this->assertTrue($this->client->removeFile($file['id']));
        $this->assertEmpty($this->client->getAllFiles(1));
    }

    public function testRemoveAllFiles()
    {
        $this->assertNotFalse($this->client->createFile(1, $this->getTaskId(), 'My file 1', base64_encode('plain text file')));
        $this->assertNotFalse($this->client->createFile(1, $this->getTaskId(), 'My file 2', base64_encode('plain text file')));

        $files = $this->client->getAllFiles(array('task_id' => $this->getTaskId()));
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);

        $this->assertTrue($this->client->removeAllFiles(array('task_id' => $this->getTaskId())));

        $files = $this->client->getAllFiles(array('task_id' => $this->getTaskId()));
        $this->assertEmpty($files);
    }

    public function testCreateTaskWithReference()
    {
        $task = array(
            'title' => 'Task with external ticket number',
            'reference' => 'TICKET-1234',
            'project_id' => 1,
            'description' => '[Link to my ticket](http://my-ticketing-system/1234)',
        );

        $task_id = $this->client->createTask($task);

        $this->assertNotFalse($task_id);
        $this->assertInternalType('int', $task_id);
        $this->assertTrue($task_id > 0);
    }

    public function testGetTaskByReference()
    {
        $task = $this->client->getTaskByReference(array('project_id' => 1, 'reference' => 'TICKET-1234'));

        $this->assertNotEmpty($task);
        $this->assertEquals('Task with external ticket number', $task['title']);
        $this->assertEquals('TICKET-1234', $task['reference']);
        $this->assertEquals('http://127.0.0.1:8000/?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'], $task['url']);
    }

    public function testCreateOverdueTask()
    {
        $this->assertNotFalse($this->client->createTask(array(
            'title' => 'overdue task',
            'project_id' => 1,
            'date_due' => date('Y-m-d', strtotime('-2days')),
        )));
    }

    public function testGetOverdueTasksByProject()
    {
        $tasks = $this->client->getOverdueTasksByProject(1);
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('overdue task', $tasks[0]['title']);
        $this->assertEquals('API test', $tasks[0]['project_name']);
    }

    public function testGetOverdueTasks()
    {
        $tasks = $this->client->getOverdueTasks();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('overdue task', $tasks[0]['title']);
        $this->assertEquals('API test', $tasks[0]['project_name']);
    }
}
