<?php

use Kanboard\Model\ProjectModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskModel;
use Kanboard\Pagination\SubtaskPagination;

require_once __DIR__.'/../Base.php';

class SubtaskPaginationTest extends Base
{
    public function testDashboardPagination()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskPagination = new SubtaskPagination($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask #1', 'user_id' => 1)));
        $this->assertEquals(2, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask #1', 'user_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask #1', 'user_id' => 1)));
        $this->assertEquals(4, $subtaskModel->create(array('task_id' => 2, 'title' => 'subtask #1')));
        $this->assertEquals(5, $subtaskModel->create(array('task_id' => 1, 'title' => 'subtask #1')));

        $this->assertCount(3, $subtaskPagination->getDashboardPaginator(1, 'subtasks', 5)->getCollection());
        $this->assertCount(0, $subtaskPagination->getDashboardPaginator(2, 'subtasks', 5)->getCollection());
        $this->assertCount(3, $subtaskPagination->getDashboardPaginator(1, 'subtasks', 5)->setOrder(TaskModel::TABLE.'.id')->getCollection());
        $this->assertCount(3, $subtaskPagination->getDashboardPaginator(1, 'subtasks', 5)->setOrder('project_name')->getCollection());
        $this->assertCount(3, $subtaskPagination->getDashboardPaginator(1, 'subtasks', 5)->setOrder('task_name')->getCollection());
        $this->assertCount(3, $subtaskPagination->getDashboardPaginator(1, 'subtasks', 5)->setOrder(SubtaskModel::TABLE.'.title')->getCollection());
    }
}
