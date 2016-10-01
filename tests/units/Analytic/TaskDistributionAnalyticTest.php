<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Analytic\TaskDistributionAnalytic;

class TaskDistributionAnalyticTest extends Base
{
    public function testBuild()
    {
        $projectModel = new ProjectModel($this->container);
        $taskDistributionModel = new TaskDistributionAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->createTasks(1, 20, 1);
        $this->createTasks(2, 30, 1);
        $this->createTasks(3, 40, 1);
        $this->createTasks(4, 10, 1);

        $expected = array(
            array(
                'column_title' => 'Backlog',
                'nb_tasks' => 20,
                'percentage' => 20.0,
            ),
            array(
                'column_title' => 'Ready',
                'nb_tasks' => 30,
                'percentage' => 30.0,
            ),
            array(
                'column_title' => 'Work in progress',
                'nb_tasks' => 40,
                'percentage' => 40.0,
            ),
            array(
                'column_title' => 'Done',
                'nb_tasks' => 10,
                'percentage' => 10.0,
            )
        );

        $this->assertEquals($expected, $taskDistributionModel->build(1));
    }

    private function createTasks($column_id, $nb_active, $nb_inactive)
    {
        $taskCreationModel = new TaskCreationModel($this->container);

        for ($i = 0; $i < $nb_active; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => $column_id, 'is_active' => 1)));
        }

        for ($i = 0; $i < $nb_inactive; $i++) {
            $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'column_id' => $column_id, 'is_active' => 0)));
        }
    }
}
