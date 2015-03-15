<?php

require_once __DIR__.'/Base.php';

use Model\TaskFinder;
use Model\TaskCreation;
use Model\Subtask;
use Model\SubtaskTimeTracking;
use Model\Project;
use Model\Category;
use Model\User;

class SubtaskTimeTrackingTest extends Base
{
    public function testLogStartTime()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertTrue($st->logStartTime(1, 1));

        $timesheet = $st->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(1, $timesheet);
        $this->assertNotEmpty($timesheet[0]['start']);
        $this->assertEmpty($timesheet[0]['end']);
        $this->assertEquals(1, $timesheet[0]['user_id']);
        $this->assertEquals(1, $timesheet[0]['subtask_id']);
    }

    public function testLogStartEnd()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        // No start time
        $this->assertTrue($st->logEndTime(1, 1));
        $timesheet = $st->getUserTimesheet(1);
        $this->assertEmpty($timesheet);

        // Log start and end time
        $this->assertTrue($st->logStartTime(1, 1));
        sleep(1);
        $this->assertTrue($st->logEndTime(1, 1));

        $timesheet = $st->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(1, $timesheet);
        $this->assertNotEmpty($timesheet[0]['start']);
        $this->assertNotEmpty($timesheet[0]['end']);
        $this->assertEquals(1, $timesheet[0]['user_id']);
        $this->assertEquals(1, $timesheet[0]['subtask_id']);
        $this->assertNotEquals($timesheet[0]['start'], $timesheet[0]['end']);
    }

    public function testCalculateSubtaskTime()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 2.2, 'time_estimated' => 3.3)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 1.1, 'time_estimated' => 4.4)));

        $time = $st->calculateSubtaskTime(1);
        $this->assertNotempty($time);
        $this->assertCount(2, $time);
        $this->assertEquals(3.3, $time['total_spent'], 'Total spent', 0.01);
        $this->assertEquals(7.7, $time['total_estimated'], 'Total estimated', 0.01);
    }

    public function testUpdateSubtaskTimeSpent()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 2.2)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($st->logStartTime(1, 1));
        $this->assertTrue($st->logStartTime(2, 1));

        // Fake start time
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->update(array('start' => time() - 3600));

        $this->assertTrue($st->logEndTime(1, 1));
        $this->assertTrue($st->logEndTime(2, 1));

        $timesheet = $st->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(2, $timesheet);
        $this->assertEquals(3600, $timesheet[0]['end'] - $timesheet[0]['start'], 'Wrong timestamps', 1);
        $this->assertEquals(3600, $timesheet[1]['end'] - $timesheet[1]['start'], 'Wrong timestamps', 1);

        $time = $st->calculateSubtaskTime(1);
        $this->assertNotempty($time);
        $this->assertEquals(4.2, $time['total_spent'], 'Total spent', 0.01);
        $this->assertEquals(0, $time['total_estimated'], 'Total estimated', 0.01);

        $time = $st->calculateSubtaskTime(2);
        $this->assertNotempty($time);
        $this->assertEquals(0, $time['total_spent'], 'Total spent', 0.01);
        $this->assertEquals(0, $time['total_estimated'], 'Total estimated', 0.01);
    }

    public function testUpdateTaskTimeTracking()
    {
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'test 2', 'project_id' => 1, 'time_estimated' => 1.5, 'time_spent' => 0.5)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_spent' => 2.2)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => 1)));

        $st->updateTaskTimeTracking(1);
        $st->updateTaskTimeTracking(2);

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2.2, $task['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(1, $task['time_estimated'], 'Total estimated', 0.01);

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0.5, $task['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(1.5, $task['time_estimated'], 'Total estimated', 0.01);
    }

    public function testGetCalendarEvents()
    {
        $tf = new TaskFinder($this->container);
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $st = new SubtaskTimeTracking($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'test 1', 'project_id' => 2)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 1)));

        $this->assertEquals(4, $s->create(array('title' => 'subtask #4', 'task_id' => 2)));
        $this->assertEquals(5, $s->create(array('title' => 'subtask #5', 'task_id' => 2)));
        $this->assertEquals(6, $s->create(array('title' => 'subtask #6', 'task_id' => 2)));
        $this->assertEquals(7, $s->create(array('title' => 'subtask #7', 'task_id' => 2)));
        $this->assertEquals(8, $s->create(array('title' => 'subtask #8', 'task_id' => 2)));

        // Slot start before and finish inside the calendar time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 1, 'start' => strtotime('-1 day'), 'end' => strtotime('+1 hour')));

        // Slot start inside time range and finish after the time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 2, 'start' => strtotime('+1 hour'), 'end' => strtotime('+2 days')));

        // Start before time range and finish inside time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 3, 'start' => strtotime('-1 day'), 'end' => strtotime('+1.5 days')));

        // Start and finish inside time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 4, 'start' => strtotime('+1 hour'), 'end' => strtotime('+2 hours')));

        // Start and finish after the time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 5, 'start' => strtotime('+2 days'), 'end' => strtotime('+3 days')));

        // Start and finish before the time range
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 6, 'start' => strtotime('-2 days'), 'end' => strtotime('-1 day')));

        // Start before time range and not finished
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 7, 'start' => strtotime('-1 day')));

        // Start inside time range and not finish
        $this->container['db']->table(SubtaskTimeTracking::TABLE)->insert(array('user_id' => 1, 'subtask_id' => 8, 'start' => strtotime('+3200 seconds')));

        $timesheet = $st->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(8, $timesheet);

        $events = $st->getUserCalendarEvents(1, date('Y-m-d'), date('Y-m-d', strtotime('+2 day')));
        $this->assertNotEmpty($events);
        $this->assertCount(6, $events);
        $this->assertEquals(1, $events[0]['subtask_id']);
        $this->assertEquals(2, $events[1]['subtask_id']);
        $this->assertEquals(3, $events[2]['subtask_id']);
        $this->assertEquals(4, $events[3]['subtask_id']);
        $this->assertEquals(7, $events[4]['subtask_id']);
        $this->assertEquals(8, $events[5]['subtask_id']);

        $events = $st->getProjectCalendarEvents(1, date('Y-m-d'), date('Y-m-d', strtotime('+2 days')));
        $this->assertNotEmpty($events);
        $this->assertCount(3, $events);
        $this->assertEquals(1, $events[0]['subtask_id']);
        $this->assertEquals(2, $events[1]['subtask_id']);
        $this->assertEquals(3, $events[2]['subtask_id']);

        $events = $st->getProjectCalendarEvents(2, date('Y-m-d'), date('Y-m-d', strtotime('+2 days')));
        $this->assertNotEmpty($events);
        $this->assertCount(3, $events);
        $this->assertEquals(4, $events[0]['subtask_id']);
        $this->assertEquals(7, $events[1]['subtask_id']);
        $this->assertEquals(8, $events[2]['subtask_id']);
    }
}
