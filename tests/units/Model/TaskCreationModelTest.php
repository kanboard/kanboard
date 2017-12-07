<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;

class TaskCreationModelTest extends Base
{
    public function onCreate($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('test', $event_data['task']['title']);
    }

    public function testNoTitle()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, function () {});
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE, function () {});

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE.'.closure', $called);

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals('Untitled', $task['title']);
        $this->assertEquals(1, $task['project_id']);
    }

    public function testMinimum()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $finderModel = new TaskFinderModel($this->container);

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, function () {});
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE, array($this, 'onCreate'));

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE.'.TaskCreationModelTest::onCreate', $called);

        $task = $finderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('yellow', $task['color_id']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['creator_id']);

        $this->assertEquals('test', $task['title']);
        $this->assertEquals('', $task['description']);
        $this->assertEquals('', $task['reference']);

        $this->assertEquals(time(), $task['date_creation'], 'Wrong timestamp', 1);
        $this->assertEquals(time(), $task['date_modification'], 'Wrong timestamp', 1);
        $this->assertEquals(0, $task['date_due']);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(0, $task['date_started']);

        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(0, $task['time_spent']);

        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['is_active']);
        $this->assertEquals(0, $task['score']);
    }

    public function testColorId()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'color_id' => 'blue')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('blue', $task['color_id']);
    }

    public function testOwnerId()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'owner_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function testCategoryId()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'category_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testCreatorId()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'creator_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['creator_id']);
    }

    public function testThatCreatorIsDefined()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $_SESSION['user'] = array('id' => 1);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1, $task['creator_id']);
    }

    public function testColumnId()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);
    }

    public function testPosition()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(1, $task['position']);

        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => 2)));

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);

        $this->assertEquals(2, $task['id']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['position']);
    }

    public function testDescription()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'description' => 'test')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('test', $task['description']);
    }

    public function testReference()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'reference' => 'test')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);

        $this->assertEquals(1, $task['id']);
        $this->assertEquals('test', $task['reference']);
    }

    public function testDateDue()
    {
        $date = '2014-11-23 14:30';
        $timestamp = strtotime('+2days');
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => $date)));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => $timestamp)));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => '')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals($date, date('Y-m-d H:i', $task['date_due']));

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(date('Y-m-d H:i', $timestamp), date('Y-m-d H:i', $task['date_due']));

        $task = $taskFinderModel->getById(3);
        $this->assertEquals(3, $task['id']);
        $this->assertEquals(0, $task['date_due']);
    }

    public function testDateStarted()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));

        // Set only a date
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_started' => '2014-11-24')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('2014-11-24 '.date('H:i'), date('Y-m-d H:i', $task['date_started']));

        // Set a datetime
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_started' => '2014-11-24 16:25')));

        $task = $taskFinderModel->getById(2);
        $this->assertEquals('2014-11-24 16:25', date('Y-m-d H:i', $task['date_started']));

        // Set a datetime
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_started' => '11/24/2014 18:25')));

        $task = $taskFinderModel->getById(3);
        $this->assertEquals('2014-11-24 18:25', date('Y-m-d H:i', $task['date_started']));

        // Set a timestamp
        $this->assertEquals(4, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_started' => time())));

        $task = $taskFinderModel->getById(4);
        $this->assertEquals(time(), $task['date_started'], '', 1);

        // Set empty string
        $this->assertEquals(5, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_started' => '')));
        $task = $taskFinderModel->getById(5);
        $this->assertEquals(0, $task['date_started']);
    }

    public function testTime()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.5, 'time_spent' => 2.3)));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => '', 'time_spent' => '')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals(1.5, $task['time_estimated']);
        $this->assertEquals(2.3, $task['time_spent']);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['id']);
        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(0, $task['time_spent']);
    }

    public function testStripColumn()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'another_task' => '1')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
    }

    public function testScore()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'score' => '3')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotFalse($task);
        $this->assertEquals(3, $task['score']);
    }

    public function testDefaultColor()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $configModel = new ConfigModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('yellow', $task['color_id']);

        $this->assertTrue($configModel->save(array('default_color' => 'orange')));
        $this->container['memoryCache']->flush();

        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2')));

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals('orange', $task['color_id']);
    }

    public function testDueDateYear2038TimestampBug()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'date_due' => strtotime('2050-01-10 12:30'))));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals('2050-01-10 12:30', date('Y-m-d H:i', $task['date_due']));
    }
}
