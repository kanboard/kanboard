<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ColumnModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;

class TaskFinderModelTest extends Base
{
    public function testGetDetails()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new \Kanboard\Model\CategoryModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $categoryModel->create(array('project_id' => 1, 'name' => 'C1')));
        $this->assertEquals(1, $taskCreationModel->create(array(
            'project_id' => 1,
            'title' => 'Task #1',
            'reference' => 'test',
            'description' => 'desc',
            'owner_id' => 1,
            'category_id' => 1,
        )));

        $task = $taskFinderModel->getDetails(1);
        $this->assertEquals(1, $task['id']);
        $this->assertEquals('test', $task['reference']);
        $this->assertEquals('Task #1', $task['title']);
        $this->assertEquals('desc', $task['description']);
        $this->assertEquals(time(), $task['date_creation'], 'Delta', 1);
        $this->assertEquals(time(), $task['date_modification'], 'Delta', 1);
        $this->assertEquals(time(), $task['date_moved'], 'Delta', 1);
        $this->assertEquals(0, $task['date_completed']);
        $this->assertEquals(0, $task['date_due']);
        $this->assertEquals(0, $task['date_started']);
        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(0, $task['time_spent']);
        $this->assertEquals('yellow', $task['color_id']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(0, $task['creator_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(0, $task['score']);
        $this->assertEquals(1, $task['category_id']);
        $this->assertEquals(0, $task['priority']);
        $this->assertEquals(1, $task['swimlane_id']);
        $this->assertEquals(TaskModel::RECURRING_STATUS_NONE, $task['recurrence_status']);
        $this->assertEquals(TaskModel::RECURRING_TRIGGER_FIRST_COLUMN, $task['recurrence_trigger']);
        $this->assertEquals(0, $task['recurrence_factor']);
        $this->assertEquals(TaskModel::RECURRING_TIMEFRAME_DAYS, $task['recurrence_timeframe']);
        $this->assertEquals(TaskModel::RECURRING_BASEDATE_DUEDATE, $task['recurrence_basedate']);
        $this->assertEquals(0, $task['recurrence_parent']);
        $this->assertEquals(0, $task['recurrence_child']);
        $this->assertEquals('C1', $task['category_name']);
        $this->assertEquals('Default swimlane', $task['swimlane_name']);
        $this->assertEquals('Project #1', $task['project_name']);
        $this->assertEquals('Backlog', $task['column_title']);
        $this->assertEquals('admin', $task['assignee_username']);
        $this->assertEquals('', $task['assignee_name']);
        $this->assertEquals('', $task['creator_username']);
        $this->assertEquals('', $task['creator_name']);
    }

    public function testGetTasksForDashboardWithHiddenColumn()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1)));

        $tasks = $taskFinderModel->getUserQuery(1)->findAll();
        $this->assertCount(2, $tasks);

        $this->assertTrue($columnModel->update(2, 'Test', 0, '', 1));

        $tasks = $taskFinderModel->getUserQuery(1)->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
        $this->assertEquals(1, $tasks[0]['column_id']);

        $this->assertTrue($columnModel->update(2, 'Test', 0, '', 0));

        $tasks = $taskFinderModel->getUserQuery(1)->findAll();
        $this->assertCount(2, $tasks);
    }

    public function testGetOverdueTasks()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1)));

        $tasks = $taskFinderModel->getOverdueTasks();
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }

    public function testGetOverdueTasksByProject()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 2, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(5, $taskCreationModel->create(array('title' => 'Task #5', 'project_id' => 1)));

        $tasks = $taskFinderModel->getOverdueTasksByProject(1);
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task #1', $tasks[0]['title']);
    }

    public function testGetOverdueTasksByUser()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1, 'owner_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 2, 'owner_id' => 1, 'date_due' => strtotime('-1 day'))));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 1, 'date_due' => strtotime('+1 day'))));
        $this->assertEquals(4, $taskCreationModel->create(array('title' => 'Task #4', 'project_id' => 1, 'date_due' => 0)));
        $this->assertEquals(5, $taskCreationModel->create(array('title' => 'Task #5', 'project_id' => 1)));

        $tasks = $taskFinderModel->getOverdueTasksByUser(1);
        $this->assertNotEmpty($tasks);
        $this->assertTrue(is_array($tasks));
        $this->assertCount(2, $tasks);

        $this->assertEquals(1, $tasks[0]['id']);
        $this->assertEquals('Task #1', $tasks[0]['title']);
        $this->assertEquals(1, $tasks[0]['owner_id']);
        $this->assertEquals(1, $tasks[0]['project_id']);
        $this->assertEquals('Project #1', $tasks[0]['project_name']);
        $this->assertEquals('admin', $tasks[0]['assignee_username']);
        $this->assertEquals('', $tasks[0]['assignee_name']);

        $this->assertEquals('Task #2', $tasks[1]['title']);
    }

    public function testCountByProject()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 2)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'Task #3', 'project_id' => 2)));

        $this->assertEquals(1, $taskFinderModel->countByProjectId(1));
        $this->assertEquals(2, $taskFinderModel->countByProjectId(2));
    }

    public function testGetProjectToken()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));

        $this->assertTrue($projectModel->enablePublicAccess(1));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 2)));

        $project = $projectModel->getById(1);
        $this->assertEquals($project['token'], $taskFinderModel->getProjectToken(1));
        $this->assertEmpty($taskFinderModel->getProjectToken(2));
    }
}
