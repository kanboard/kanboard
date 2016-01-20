<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Model\Task;
use Kanboard\Analytic\AverageLeadCycleTimeAnalytic;

class AverageLeadCycleTimeAnalyticTest extends Base
{
    public function testBuild()
    {
        $taskCreationModel = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $averageLeadCycleTimeAnalytic = new AverageLeadCycleTimeAnalytic($this->container);
        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(4, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        // LT=3600 CT=1800
        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_completed' => $now + 3600, 'date_started' => $now + 1800));

        // LT=1800 CT=900
        $this->container['db']->table(Task::TABLE)->eq('id', 2)->update(array('date_completed' => $now + 1800, 'date_started' => $now + 900));

        // LT=3600 CT=0
        $this->container['db']->table(Task::TABLE)->eq('id', 3)->update(array('date_completed' => $now + 3600));

        // LT=2*3600 CT=0
        $this->container['db']->table(Task::TABLE)->eq('id', 4)->update(array('date_completed' => $now + 2 * 3600));

        $stats = $averageLeadCycleTimeAnalytic->build(1);
        $expected = array(
            'count' => 4,
            'total_lead_time' => 3600 + 1800 + 3600 + 2*3600,
            'total_cycle_time' => 1800 + 900,
            'avg_lead_time' => (3600 + 1800 + 3600 + 2*3600) / 4,
            'avg_cycle_time' => (1800 + 900) / 4,
        );

        $this->assertEquals($expected, $stats);
    }

    public function testBuildWithNoTasks()
    {
        $taskCreationModel = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $averageLeadCycleTimeAnalytic = new AverageLeadCycleTimeAnalytic($this->container);
        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $stats = $averageLeadCycleTimeAnalytic->build(1);

        $expected = array(
            'count' => 0,
            'total_lead_time' => 0,
            'total_cycle_time' => 0,
            'avg_lead_time' => 0,
            'avg_cycle_time' => 0,
        );

        $this->assertEquals($expected, $stats);
    }
}
