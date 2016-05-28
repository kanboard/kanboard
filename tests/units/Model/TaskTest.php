<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;

class TaskTest extends Base
{
    public function testRemove()
    {
        $t = new TaskModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertTrue($t->remove(1));
        $this->assertFalse($t->remove(1234));
    }

    public function testGetTaskIdFromText()
    {
        $t = new TaskModel($this->container);
        $this->assertEquals(123, $t->getTaskIdFromText('My task #123'));
        $this->assertEquals(0, $t->getTaskIdFromText('My task 123'));
    }

    public function testRecurrenceSettings()
    {
        $t = new TaskModel($this->container);

        $statuses = $t->getRecurrenceStatusList();
        $this->assertCount(2, $statuses);
        $this->assertArrayHasKey(TaskModel::RECURRING_STATUS_NONE, $statuses);
        $this->assertArrayHasKey(TaskModel::RECURRING_STATUS_PENDING, $statuses);
        $this->assertArrayNotHasKey(TaskModel::RECURRING_STATUS_PROCESSED, $statuses);

        $triggers = $t->getRecurrenceTriggerList();
        $this->assertCount(3, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_FIRST_COLUMN, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_LAST_COLUMN, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_CLOSE, $triggers);

        $dates = $t->getRecurrenceBasedateList();
        $this->assertCount(2, $dates);
        $this->assertArrayHasKey(TaskModel::RECURRING_BASEDATE_DUEDATE, $dates);
        $this->assertArrayHasKey(TaskModel::RECURRING_BASEDATE_TRIGGERDATE, $dates);

        $timeframes = $t->getRecurrenceTimeframeList();
        $this->assertCount(3, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_DAYS, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_MONTHS, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_YEARS, $timeframes);
    }
}
