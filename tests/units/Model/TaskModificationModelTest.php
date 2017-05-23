<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskModificationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskTagModel;

class TaskModificationModelTest extends Base
{
    public function onCreateUpdate($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('After', $event_data['task']['title']);
        $this->assertEquals('After', $event_data['changes']['title']);
    }

    public function onUpdate($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('After', $event_data['task']['title']);
    }

    public function onAssigneeChange($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals(1, $event_data['changes']['owner_id']);
    }

    public function testThatNoEventAreFiredWhenNoChanges()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, array($this, 'onCreateUpdate'));
        $this->container['dispatcher']->addListener(TaskModel::EVENT_UPDATE, array($this, 'onUpdate'));

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'title' => 'test')));

        $this->assertEmpty($this->container['dispatcher']->getCalledListeners());
    }

    public function testChangeTitle()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Before', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, array($this, 'onCreateUpdate'));
        $this->container['dispatcher']->addListener(TaskModel::EVENT_UPDATE, array($this, 'onUpdate'));

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'title' => 'After')));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.TaskModificationModelTest::onCreateUpdate', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_UPDATE.'.TaskModificationModelTest::onUpdate', $called);

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('After', $task['title']);
    }

    public function testChangeAssignee()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['owner_id']);

        $this->container['dispatcher']->addListener(TaskModel::EVENT_ASSIGNEE_CHANGE, array($this, 'onAssigneeChange'));

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'owner_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_ASSIGNEE_CHANGE.'.TaskModificationModelTest::onAssigneeChange', $called);

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function testChangeDescription()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('', $task['description']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'description' => 'test')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('test', $task['description']);
    }

    public function testChangeCategory()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['category_id']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'category_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(1, $task['category_id']);
    }

    public function testChangeColor()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('yellow', $task['color_id']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'color_id' => 'blue')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('blue', $task['color_id']);
    }

    public function testChangeScore()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['score']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'score' => 13)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(13, $task['score']);
    }

    public function testChangeDueDate()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['date_due']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_due' => '2014-11-24 14:30')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('2014-11-24 14:30', date('Y-m-d H:i', $task['date_due']));

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_due' => time())));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(date('Y-m-d H:i'), date('Y-m-d H:i', $task['date_due']));
    }

    public function testChangeStartedDate()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['date_started']);

        // Set only a date
        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_started' => '2014-11-24')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('2014-11-24 '.date('H:i'), date('Y-m-d H:i', $task['date_started']));

        // Set a datetime
        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_started' => '2014-11-24 16:25')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('2014-11-24 16:25', date('Y-m-d H:i', $task['date_started']));

        // Set a datetime
        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_started' => '11/24/2014 18:25')));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals('2014-11-24 18:25', date('Y-m-d H:i', $task['date_started']));

        // Set a timestamp
        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'date_started' => time())));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(time(), $task['date_started'], '', 1);
    }

    public function testChangeTimeEstimated()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['time_estimated']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'time_estimated' => 13.3)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(13.3, $task['time_estimated']);
    }

    public function testChangeTimeSpent()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(0, $task['time_spent']);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'time_spent' => 13.3)));

        $task = $taskFinderModel->getById(1);
        $this->assertEquals(13.3, $task['time_spent']);
    }

    public function testChangeTags()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'tags' => array('tag1', 'tag2'))));
        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'tags' => array('tag2'))));

        $tags = $taskTagModel->getList(1);
        $this->assertEquals(array(2 => 'tag2'), $tags);
    }

    public function testRemoveAllTags()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskModificationModel = new TaskModificationModel($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test1', 'project_id' => 1, 'tags' => array('tag1', 'tag2'))));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'test2', 'project_id' => 1, 'tags' => array('tag1', 'tag2'))));

        $this->assertTrue($taskModificationModel->update(array('id' => 1)));
        $tags = $taskTagModel->getList(1);
        $this->assertEquals(array(1 => 'tag1', 2 => 'tag2'), $tags);

        $this->assertTrue($taskModificationModel->update(array('id' => 1, 'tags' => array())));
        $tags = $taskTagModel->getList(1);
        $this->assertEquals(array(), $tags);

        $this->assertTrue($taskModificationModel->update(array('id' => 2, 'tags' => array(''))));
        $tags = $taskTagModel->getList(2);
        $this->assertEquals(array(), $tags);
    }
}
