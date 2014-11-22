<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\ProjectDailySummary;
use Model\Task;
use Model\TaskCreation;

class ProjectDailySummaryTest extends Base
{
    public function testUpdateTotals()
    {
        $p = new Project($this->container);
        $pds = new ProjectDailySummary($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(0, $pds->countDays(1, date('Y-m-d', strtotime('-2days')), date('Y-m-d')));

        for ($i = 0; $i < 10; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 1)));
        }

        for ($i = 0; $i < 5; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 4)));
        }

        $pds->updateTotals(1, date('Y-m-d', strtotime('-2days')));

        for ($i = 0; $i < 15; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 3)));
        }

        for ($i = 0; $i < 25; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 2)));
        }

        $pds->updateTotals(1, date('Y-m-d', strtotime('-1 day')));

        $this->assertNotFalse($t->close(1));
        $this->assertNotFalse($t->close(2));

        for ($i = 0; $i < 3; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 3)));
        }

        for ($i = 0; $i < 5; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 2)));
        }

        for ($i = 0; $i < 4; $i++) {
            $this->assertNotFalse($tc->create(array('title' => 'Task #'.$i, 'project_id' => 1, 'column_id' => 4)));
        }

        $pds->updateTotals(1, date('Y-m-d'));

        $this->assertEquals(3, $pds->countDays(3, date('Y-m-d', strtotime('-2days')), date('Y-m-d')));
        $metrics = $pds->getAggregatedMetrics(1, date('Y-m-d', strtotime('-2days')), date('Y-m-d'));

        $this->assertNotEmpty($metrics);
        $this->assertEquals(4, count($metrics));
        $this->assertEquals(5, count($metrics[0]));
        $this->assertEquals('Backlog', $metrics[0][1]);

        $this->assertEquals(date('Y-m-d', strtotime('-2days')), $metrics[1][0]);
        $this->assertEquals(10, $metrics[1][1]);
        $this->assertEquals(0, $metrics[1][2]);
        $this->assertEquals(0, $metrics[1][3]);
        $this->assertEquals(5, $metrics[1][4]);

        $this->assertEquals(date('Y-m-d', strtotime('-1day')), $metrics[2][0]);
        $this->assertEquals(10, $metrics[2][1]);
        $this->assertEquals(25, $metrics[2][2]);
        $this->assertEquals(15, $metrics[2][3]);
        $this->assertEquals(5, $metrics[2][4]);

        $this->assertEquals(date('Y-m-d'), $metrics[3][0]);
        $this->assertEquals(8, $metrics[3][1]);
        $this->assertEquals(30, $metrics[3][2]);
        $this->assertEquals(18, $metrics[3][3]);
        $this->assertEquals(9, $metrics[3][4]);
    }
}
