<?php

use Kanboard\Core\Http\Request;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskModel;
use Kanboard\Pagination\TaskPagination;

require_once __DIR__.'/../Base.php';

class TaskPaginationTest extends Base
{
    public function testDashboardPagination()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskPagination = new TaskPagination($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1)));

        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->getCollection());
        $this->assertCount(0, $taskPagination->getDashboardPaginator(2, 'tasks', 5)->getCollection());
        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->setOrder(TaskModel::TABLE.'.id')->getCollection());
        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->setOrder('project_name')->getCollection());
        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->setOrder(TaskModel::TABLE.'.title')->getCollection());
        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->setOrder(TaskModel::TABLE.'.priority')->getCollection());
        $this->assertCount(1, $taskPagination->getDashboardPaginator(1, 'tasks', 5)->setOrder(TaskModel::TABLE.'.date_due')->getCollection());
    }

    public function testTaskPaginationTotal() {

        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $numTasks = 11;
        foreach (range(1,$numTasks) as $i) {
            $this->assertEquals($i, $taskCreationModel->create(array('title' => 'Task #'.$i, 'project_id' => 1)));
        }

        $taskPaginationTotal = new TaskPagination($this->container);
        $this->assertEquals($numTasks, $taskPaginationTotal->getDashboardPaginator(0, 'tasks', 5)->getTotal());
    }

    public function testTaskPaginationPaging() {

        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));

        $numTasks = 12;
        foreach (range(1,$numTasks) as $i) {
            $this->assertEquals($i, $taskCreationModel->create(array('title' => 'Task #'.$i, 'project_id' => 1)));
        }

        $taskPaginationMaxM = new TaskPagination($this->container);
        foreach (range(1,$numTasks) as $m) {
            foreach(range(1, (int) ceil($numTasks / $m)) as $p) {
                $this->container['request'] = new Request($this->container, array(), array('page' => $p), array(), array(), array());
                $this->assertEquals(1+($p-1)*$m , $taskPaginationMaxM->getDashboardPaginator(0, 'tasks', $m)->calculate()->getCollection()[0]['id']);
            }
        }
    }
}
