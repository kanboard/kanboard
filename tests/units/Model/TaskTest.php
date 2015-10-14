<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\TaskStatus;
use Kanboard\Model\Project;
use Kanboard\Model\ProjectPermission;
use Kanboard\Model\Category;
use Kanboard\Model\User;

class TaskTest extends Base
{
    public function testRemove()
    {
        $t = new Task($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFinder($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertTrue($t->remove(1));
        $this->assertFalse($t->remove(1234));
    }

    public function testGetTaskIdFromText()
    {
        $t = new Task($this->container);
        $this->assertEquals(123, $t->getTaskIdFromText('My task #123'));
        $this->assertEquals(0, $t->getTaskIdFromText('My task 123'));
    }

    public function testRecurrenceSettings()
    {
        $t = new Task($this->container);

        $statuses = $t->getRecurrenceStatusList();
        $this->assertCount(2, $statuses);
        $this->assertArrayHasKey(Task::RECURRING_STATUS_NONE, $statuses);
        $this->assertArrayHasKey(Task::RECURRING_STATUS_PENDING, $statuses);
        $this->assertArrayNotHasKey(Task::RECURRING_STATUS_PROCESSED, $statuses);

        $triggers = $t->getRecurrenceTriggerList();
        $this->assertCount(3, $triggers);
        $this->assertArrayHasKey(Task::RECURRING_TRIGGER_FIRST_COLUMN, $triggers);
        $this->assertArrayHasKey(Task::RECURRING_TRIGGER_LAST_COLUMN, $triggers);
        $this->assertArrayHasKey(Task::RECURRING_TRIGGER_CLOSE, $triggers);

        $dates = $t->getRecurrenceBasedateList();
        $this->assertCount(2, $dates);
        $this->assertArrayHasKey(Task::RECURRING_BASEDATE_DUEDATE, $dates);
        $this->assertArrayHasKey(Task::RECURRING_BASEDATE_TRIGGERDATE, $dates);

        $timeframes = $t->getRecurrenceTimeframeList();
        $this->assertCount(3, $timeframes);
        $this->assertArrayHasKey(Task::RECURRING_TIMEFRAME_DAYS, $timeframes);
        $this->assertArrayHasKey(Task::RECURRING_TIMEFRAME_MONTHS, $timeframes);
        $this->assertArrayHasKey(Task::RECURRING_TIMEFRAME_YEARS, $timeframes);
    }
}
