<?php

require_once __DIR__.'/../../vendor/JsonRPC/Client.php';

class Api extends PHPUnit_Framework_TestCase
{
    const URL = 'http://localhost:8000/jsonrpc.php';
    const KEY = '19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929';

    private $client;

    public function setUp()
    {
        $this->client = new JsonRPC\Client(self::URL, 5, true);
        $this->client->authentication('jsonrpc', self::KEY);

        $pdo = new PDO('sqlite:data/db.sqlite');
        $pdo->exec('UPDATE config SET api_token="'.self::KEY.'"');
    }

    public function testRemoveAll()
    {
        $projects = $this->client->getAllProjects();

        if ($projects) {
            foreach ($projects as $project) {
                $this->client->removeProject($project['id']);
            }
        }
    }

    public function testCreateProject()
    {
        $this->assertTrue($this->client->createProject('API test'));
    }

    public function testGetProjectById()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['id']);
    }

    public function testUpdateProject()
    {
        $project = $this->client->getProjectById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($this->client->updateProject(array('id' => 1, 'name' => 'API test 2', 'is_active' => 0)));

        $project = $this->client->getProjectById(1);
        $this->assertEquals('API test 2', $project['name']);
        $this->assertEquals(0, $project['is_active']);

        $this->assertTrue($this->client->updateProject(array('id' => 1, 'name' => 'API test', 'is_active' => 1)));

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
        $this->assertTrue($this->client->updateColumn(4, array('title' => 'Boo', 'task_limit' => 2)));

        $columns = $this->client->getColumns(1);
        $this->assertTrue(is_array($columns));
        $this->assertEquals('Boo', $columns[3]['title']);
        $this->assertEquals(2, $columns[3]['task_limit']);
    }

    public function testAddColumn()
    {
        $this->assertTrue($this->client->addColumn(1, array('title' => 'New column')));

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

        $this->assertTrue($this->client->createTask($task));

        $task = array(
            'title' => 'Task #1',
            'color_id' => 'blue',
            'owner_id' => 1,
        );

        $this->assertFalse($this->client->createTask($task));
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
        $tasks = $this->client->getAllTasks(1, array(1));

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEquals('Task #1', $tasks[0]['title']);

        $tasks = $this->client->getAllTasks(2, array(1, 2));

        $this->assertNotFalse($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertEmpty($tasks);
    }

    public function testUpdateTask()
    {
        $task = $this->client->getTask(1);
        $task['color_id'] = 'green';
        $task['column_id'] = 1;
        $task['description'] = 'test';
        $task['date_due'] = '';

        $this->assertTrue($this->client->updateTask($task));
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
            'confirmation' => '123456',
        );

        $this->assertTrue($this->client->createUser($user));

        $user = array(
            'username' => 'titi',
            'name' => 'Titi',
            'password' => '123456',
            'confirmation' => '789',
        );

        $this->assertFalse($this->client->createUser($user));
    }

    public function testGetUser()
    {
        $user = $this->client->getUser(2);

        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('toto', $user['username']);
    }

    public function testUpdateUser()
    {
        $user = $this->client->getUser(2);
        $user['username'] = 'titi';
        $user['name'] = 'Titi';
        unset($user['password']);

        $this->assertTrue($this->client->updateUser($user));

        $user = $this->client->getUser(2);

        $this->assertNotFalse($user);
        $this->assertTrue(is_array($user));
        $this->assertEquals('titi', $user['username']);
        $this->assertEquals('Titi', $user['name']);
    }

    public function testGetAllowedUsers()
    {
        $users = $this->client->getAllowedUsers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(1 => 'admin', 2 => 'titi'), $users);
    }

    public function testAllowedUser()
    {
        $this->assertTrue($this->client->allowUser(1, 2));

        $users = $this->client->getAllowedUsers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(2 => 'titi'), $users);
    }

    public function testRevokeUser()
    {
        $this->assertTrue($this->client->revokeUser(1, 2));

        $users = $this->client->getAllowedUsers(1);
        $this->assertNotFalse($users);
        $this->assertEquals(array(1 => 'admin', 2 => 'titi'), $users);
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

        $this->assertTrue($this->client->createTask($task));

        $comment = array(
            'task_id' => 1,
            'user_id' => 2,
            'comment' => 'boo',
        );

        $this->assertTrue($this->client->createComment($comment));
    }

    public function testGetComment()
    {
        $comment = $this->client->getComment(1);
        $this->assertNotFalse($comment);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['task_id']);
        $this->assertEquals(2, $comment['user_id']);
        $this->assertEquals('boo', $comment['comment']);
    }

    public function testUpdateComment()
    {
        $comment = $this->client->getComment(1);
        $comment['comment'] = 'test';

        $this->assertTrue($this->client->updateComment($comment));

        $comment = $this->client->getComment(1);
        $this->assertEquals('test', $comment['comment']);
    }

    public function testGetAllComments()
    {
        $comment = array(
            'task_id' => 1,
            'user_id' => 1,
            'comment' => 'blabla',
        );

        $this->assertTrue($this->client->createComment($comment));

        $comments = $this->client->getAllComments(1);
        $this->assertNotFalse($comments);
        $this->assertNotEmpty($comments);
        $this->assertTrue(is_array($comments));
        $this->assertEquals(2, count($comments));
    }

    public function testRemoveComment()
    {
        $this->assertTrue($this->client->removeComment(1));

        $comments = $this->client->getAllComments(1);
        $this->assertNotFalse($comments);
        $this->assertNotEmpty($comments);
        $this->assertTrue(is_array($comments));
        $this->assertEquals(1, count($comments));
    }

    public function testCreateSubtask()
    {
        $subtask = array(
            'task_id' => 1,
            'title' => 'subtask #1',
        );

        $this->assertTrue($this->client->createSubtask($subtask));
    }

    public function testGetSubtask()
    {
        $subtask = $this->client->getSubtask(1);
        $this->assertNotFalse($subtask);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['task_id']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
    }

    public function testUpdateSubtask()
    {
        $subtask = $this->client->getSubtask(1);
        $subtask['title'] = 'test';

        $this->assertTrue($this->client->updateSubtask($subtask));

        $subtask = $this->client->getSubtask(1);
        $this->assertEquals('test', $subtask['title']);
    }

    public function testGetAllSubtasks()
    {
        $subtask = array(
            'task_id' => 1,
            'user_id' => 2,
            'title' => 'Subtask #2',
        );

        $this->assertTrue($this->client->createSubtask($subtask));

        $subtasks = $this->client->getAllSubtasks(1);
        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertTrue(is_array($subtasks));
        $this->assertEquals(2, count($subtasks));
    }

    public function testRemoveSubtask()
    {
        $this->assertTrue($this->client->removeSubtask(1));

        $subtasks = $this->client->getAllSubtasks(1);
        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertTrue(is_array($subtasks));
        $this->assertEquals(1, count($subtasks));
    }
/*
    public function testAutomaticActions()
    {
        $task = array(
            'title' => 'Task #1',
            'color_id' => 'blue',
            'owner_id' => 0,
            'project_id' => 1,
            'column_id' => 1,
        );

        $this->assertTrue($this->client->createTask($task));

        $tasks = $this->client->getAllTasks(1, array(1));
        $task = $tasks[count($tasks) - 1];
        $task['column_id'] = 3;

        $this->assertTrue($this->client->updateTask($task));
    }*/
}
