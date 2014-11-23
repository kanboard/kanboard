<?php

require_once __DIR__.'/Base.php';

use Model\SubTask;
use Model\TaskModification;
use Model\TaskCreation;
use Model\TaskFinder;
use Model\Project;
use Model\TimeTracking;

class TimeTrackingTest extends Base
{
    public function testCalculateTime()
    {
        $tm = new TaskModification($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);
        $s = new SubTask($this->container);
        $ts = new TimeTracking($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'time_estimated' => 4.5)));
        $this->assertTrue($tm->update(array('id' => 1, 'time_spent' => 3.5)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(4.5, $task['time_estimated']);
        $this->assertEquals(3.5, $task['time_spent']);

        $timesheet = $ts->getTaskTimesheet($task, array());
        $this->assertNotEmpty($timesheet);
        $this->assertEquals(4.5, $timesheet['time_estimated']);
        $this->assertEquals(3.5, $timesheet['time_spent']);
        $this->assertEquals(1, $timesheet['time_remaining']);

        // Subtasks calculation
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_estimated' => 5.5, 'time_spent' => 3)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => '', 'time_spent' => 4)));

        $timesheet = $ts->getTaskTimesheet($task, $s->getAll(1));
        $this->assertNotEmpty($timesheet);
        $this->assertEquals(5.5, $timesheet['time_estimated']);
        $this->assertEquals(7, $timesheet['time_spent']);
        $this->assertEquals(-1.5, $timesheet['time_remaining']);
    }
}
