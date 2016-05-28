<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TransitionModel;
use Kanboard\Model\ProjectModel;

class TransitionTest extends Base
{
    public function testSave()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $transitionModel = new TransitionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $task_event = array(
            'project_id' => 1,
            'task_id' => 1,
            'src_column_id' => 1,
            'dst_column_id' => 2,
            'date_moved' => time() - 3600
        );

        $this->assertTrue($transitionModel->save(1, $task_event));

        $transitions = $transitionModel->getAllByTask(1);
        $this->assertCount(1, $transitions);
        $this->assertEquals('Backlog', $transitions[0]['src_column']);
        $this->assertEquals('Ready', $transitions[0]['dst_column']);
        $this->assertEquals('', $transitions[0]['name']);
        $this->assertEquals('admin', $transitions[0]['username']);
        $this->assertEquals(1, $transitions[0]['user_id']);
        $this->assertEquals(time(), $transitions[0]['date'], '', 3);
        $this->assertEquals(3600, $transitions[0]['time_spent']);
    }

    public function testGetTimeSpentByTask()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $transitionModel = new TransitionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $task_event = array(
            'project_id' => 1,
            'task_id' => 1,
            'src_column_id' => 1,
            'dst_column_id' => 2,
            'date_moved' => time() - 3600
        );

        $this->assertTrue($transitionModel->save(1, $task_event));

        $task_event = array(
            'project_id' => 1,
            'task_id' => 1,
            'src_column_id' => 2,
            'dst_column_id' => 3,
            'date_moved' => time() - 1200
        );

        $this->assertTrue($transitionModel->save(1, $task_event));

        $expected = array(
            '1' => 3600,
            '2' => 1200,
        );

        $this->assertEquals($expected, $transitionModel->getTimeSpentByTask(1));
    }

    public function testGetAllByProject()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $transitionModel = new TransitionModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2')));

        $task_event = array(
            'project_id' => 1,
            'src_column_id' => 1,
            'dst_column_id' => 2,
            'date_moved' => time() - 3600
        );

        $this->assertTrue($transitionModel->save(1, array('task_id' => 1) + $task_event));
        $this->assertTrue($transitionModel->save(1, array('task_id' => 2) + $task_event));

        $task_event = array(
            'project_id' => 1,
            'src_column_id' => 2,
            'dst_column_id' => 3,
            'date_moved' => time() - 1200
        );

        $this->assertTrue($transitionModel->save(1, array('task_id' => 1) + $task_event));
        $this->assertTrue($transitionModel->save(1, array('task_id' => 2) + $task_event));

        $transitions = $transitionModel->getAllByProjectAndDate(1, date('Y-m-d'), date('Y-m-d'));
        $this->assertCount(4, $transitions);

        $this->assertEquals(2, $transitions[0]['id']);
        $this->assertEquals(1, $transitions[1]['id']);
        $this->assertEquals(2, $transitions[2]['id']);
        $this->assertEquals(1, $transitions[3]['id']);

        $this->assertEquals('test2', $transitions[0]['title']);
        $this->assertEquals('test1', $transitions[1]['title']);
        $this->assertEquals('test2', $transitions[2]['title']);
        $this->assertEquals('test1', $transitions[3]['title']);

        $this->assertEquals('Ready', $transitions[0]['src_column']);
        $this->assertEquals('Ready', $transitions[1]['src_column']);
        $this->assertEquals('Backlog', $transitions[2]['src_column']);
        $this->assertEquals('Backlog', $transitions[3]['src_column']);

        $this->assertEquals('Work in progress', $transitions[0]['dst_column']);
        $this->assertEquals('Work in progress', $transitions[1]['dst_column']);
        $this->assertEquals('Ready', $transitions[2]['dst_column']);
        $this->assertEquals('Ready', $transitions[3]['dst_column']);

        $this->assertEquals('admin', $transitions[0]['username']);
        $this->assertEquals('admin', $transitions[1]['username']);
        $this->assertEquals('admin', $transitions[2]['username']);
        $this->assertEquals('admin', $transitions[3]['username']);

        $this->assertEquals(1200, $transitions[0]['time_spent']);
        $this->assertEquals(1200, $transitions[1]['time_spent']);
        $this->assertEquals(3600, $transitions[2]['time_spent']);
        $this->assertEquals(3600, $transitions[3]['time_spent']);
    }
}
