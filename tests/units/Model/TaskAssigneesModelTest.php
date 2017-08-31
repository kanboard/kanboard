<?php

use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskAssigneesModel;

require_once __DIR__.'/../Base.php';

class TaskAssigneesModelTest extends Base
{
    public function testAssociationAndDissociation()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskAssigneesModel = new TaskAssigneesModel($this->container);
        $userModel = new userModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'user 1', 'password' => '123456', 'email' => 'user1@localhost')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'user 2','password' => '123456', 'email' => '')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3', 'name' => 'user 3','password' => '123456', 'email' => 'user3@localhost')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4', 'name' => 'user 4','password' => '123456', 'email' => '')));

        $this->assertTrue($taskAssigneesModel->save(1, array(2, 3, 4)));

        $assignees = $taskAssigneesModel->getAssigneesByTask(1);
        $this->assertCount(3, $assignees);

        $this->assertEquals(2, $assignees[0]['id']);
        $this->assertEquals('user 1', $assignees[0]['name']);

        $this->assertEquals(3, $assignees[1]['id']);
        $this->assertEquals('user 2', $assignees[1]['name']);

        $this->assertEquals(4, $assignees[2]['id']);
        $this->assertEquals('user 3', $assignees[2]['name']);

        $this->assertTrue($taskAssigneesModel->save(1, array(2, 3, 5)));

        $assignees = $taskAssigneesModel->getAssigneesByTask(1);
        $this->assertCount(3, $assignees);

        $this->assertEquals(2, $assignees[0]['id']);
        $this->assertEquals('user 1', $assignees[0]['name']);

        $this->assertEquals(3, $assignees[1]['id']);
        $this->assertEquals('user 2', $assignees[1]['name']);

        $this->assertEquals(5, $assignees[2]['id']);
        $this->assertEquals('user 4', $assignees[2]['name']);
    }

    public function testGetAssigneesForTasks()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskAssigneesModel = new TaskAssigneesModel($this->container);
        $userModel = new userModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test3')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'user 1', 'password' => '123456', 'email' => 'user1@localhost')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'user 2','password' => '123456', 'email' => '')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3', 'name' => 'user 3','password' => '123456', 'email' => 'user3@localhost')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4', 'name' => 'user 4','password' => '123456', 'email' => '')));

        $this->assertTrue($taskAssigneesModel->save(1, array(2, 3, 4)));
        $this->assertTrue($taskAssigneesModel->save(2, array(4)));

        $assignees = $taskAssigneesModel->getAssigneesByTaskIds(array(1, 2, 3));

        $expected = array(
            1 => array(
                array(
                    'id' => 2,
                    'name' => 'user 1',
                    'task_id' => 1
                ),
                array(
                    'id' => 3,
                    'name' => 'user 2',
                    'task_id' => 1
                ),
                array(
                    'id' => 4,
                    'name' => 'user 3',
                    'task_id' => 1
                ),
            ),
            2 => array(
                array(
                    'id' => 4,
                    'name' => 'user 3',
                    'task_id' => 2,
                )
            )
        );

        $this->assertEquals($expected, $assignees);
    }

    public function testGetAssigneesForTasksWithEmptyList()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskAssigneesModel = new TaskAssigneesModel($this->container);
        $userModel = new userModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'user 1', 'password' => '123456', 'email' => 'user1@localhost')));
        $this->assertEquals(3, $userModel->create(array('username' => 'user2', 'name' => 'user 2','password' => '123456', 'email' => '')));
        $this->assertEquals(4, $userModel->create(array('username' => 'user3', 'name' => 'user 3','password' => '123456', 'email' => 'user3@localhost')));
        $this->assertEquals(5, $userModel->create(array('username' => 'user4', 'name' => 'user 4','password' => '123456', 'email' => '')));

        $this->assertTrue($taskAssigneesModel->save(1, array(2, 3, 4)));

        $assignees = $taskAssigneesModel->getAssigneesByTaskIds(array());
        $this->assertEquals(array(), $assignees);
    }
}
