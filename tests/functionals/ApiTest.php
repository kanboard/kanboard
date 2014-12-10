<?php

require_once __DIR__.'/../../vendor/autoload.php';

class Api extends PHPUnit_Framework_TestCase
{
    private $client;

    public static function setUpBeforeClass()
    {
        if (DB_DRIVER === 'sqlite') {
            @unlink(DB_FILENAME);
            $pdo = new PDO('sqlite:'.DB_FILENAME);
        }
        else if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
        }
        else if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
        }

        $service = new ServiceProvider\Database;
        $service->getInstance();

        $pdo->exec("UPDATE settings SET value='".API_KEY."' WHERE option='api_token'");
        $pdo->exec("UPDATE settings SET value='Europe/Paris' WHERE option='application_timezone'");
        $pdo = null;
    }

    public function setUp()
    {
        $this->client = new JsonRPC\Client(API_URL);
        $this->client->authentication('jsonrpc', API_KEY);
        //$this->client->debug = true;
    }

    private function getTaskId()
    {
        $tasks = $this->client->getAllTasks(1, 1);
        $this->assertNotEmpty($tasks);
        $this->assertEquals(1, count($tasks));

        return $tasks[0]['id'];
    }

    public function testGetTimezone()
    {
        $timezone = $this->client->getTimezone();
        $this->assertEquals('Europe/Paris', $timezone);
    }

    public function testRemoveAll()
    {
        $projects = $this->client->getAllProjects();

        if ($projects) {
            foreach ($projects as $project) {
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
    }

    public function testGetProjectByName()
    {
        $project = $this->client->getProjectByName('API test');
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);

        $project = $this->client->getProjectByName(array('name' => 'API test'));
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);

        $project = $this->client->getProjectByName('None');
        $this->assertEmpty($project);
        $this->assertNull($project);
    }

    public function testUpdateProject()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($this->client->execute('updateProject', array('id' => 1, 'name' => 'API test 2', 'is_active' => 0)));

        $project = $this->client->getProjectById(1);
        $this->assertEquals('API test 2', $project['name']);
        $this->assertEquals(0, $project['is_active']);

        $this->assertTrue($this->client->execute('updateProject', array('id' => 1, 'name' => 'API test', 'is_active' => 1)));

        $project = $this->client->getProjectById(1);
        $this->assertEquals('API test', $project['name']);
        $this->assertEquals(1, $project['is_active']);
    }

    public function testGetBoard()
    {
        $board = $this->client->getBoard(1);
        $this->assertTrue(is_array($board));
        $this->assertEquals(4, count($board));
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
    }

    public function testGetAllTasks()
    {
        $tasks = $this->client->getAllTasks(1, 1);

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEquals('Task #1', $tasks[0]['title']);

        $tasks = $this->client->getAllTasks(2, 0);

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEmpty($tasks);
    }

    public function testUpdateTask()
    {
        $task = $this->client->getTask(1);

        $values = array();
        $values['id'] = $task['id'];
        $values['color_id'] = 'green';
        $values['column_id'] = 1;
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
}
