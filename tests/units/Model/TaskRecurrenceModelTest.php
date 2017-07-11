<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\DateParser;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskRecurrenceModel;
use Kanboard\Model\TaskTagModel;

class TaskRecurrenceModelTest extends Base
{
    public function testRecurrenceSettings()
    {
        $taskRecurrenceModel = new TaskRecurrenceModel($this->container);

        $statuses = $taskRecurrenceModel->getRecurrenceStatusList();
        $this->assertCount(2, $statuses);
        $this->assertArrayHasKey(TaskModel::RECURRING_STATUS_NONE, $statuses);
        $this->assertArrayHasKey(TaskModel::RECURRING_STATUS_PENDING, $statuses);
        $this->assertArrayNotHasKey(TaskModel::RECURRING_STATUS_PROCESSED, $statuses);

        $triggers = $taskRecurrenceModel->getRecurrenceTriggerList();
        $this->assertCount(3, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_FIRST_COLUMN, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_LAST_COLUMN, $triggers);
        $this->assertArrayHasKey(TaskModel::RECURRING_TRIGGER_CLOSE, $triggers);

        $dates = $taskRecurrenceModel->getRecurrenceBasedateList();
        $this->assertCount(2, $dates);
        $this->assertArrayHasKey(TaskModel::RECURRING_BASEDATE_DUEDATE, $dates);
        $this->assertArrayHasKey(TaskModel::RECURRING_BASEDATE_TRIGGERDATE, $dates);

        $timeframes = $taskRecurrenceModel->getRecurrenceTimeframeList();
        $this->assertCount(3, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_DAYS, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_MONTHS, $timeframes);
        $this->assertArrayHasKey(TaskModel::RECURRING_TIMEFRAME_YEARS, $timeframes);
    }

    public function testCalculateRecurringTaskDueDate()
    {
        $taskRecurrenceModel = new TaskRecurrenceModel($this->container);

        $values = array('date_due' => 0);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(0, $values['date_due']);

        $values = array('date_due' => 0, 'recurrence_factor' => 0, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(0, $values['date_due']);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(time() + 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => -2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(time() - 2 * 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1431291376 + 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => -1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1431291376 - 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_MONTHS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1436561776, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_YEARS);
        $taskRecurrenceModel->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1494449776, $values['date_due'], '', 1);
    }

    public function testDuplicateRecurringTask()
    {
        $taskRecurrenceModel = new TaskRecurrenceModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $dateParser = new DateParser($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array(
            'title' => 'test',
            'project_id' => 1,
            'date_due' => 1436561776,
            'recurrence_status' => TaskModel::RECURRING_STATUS_PENDING,
            'recurrence_trigger' => TaskModel::RECURRING_TRIGGER_CLOSE,
            'recurrence_factor' => 2,
            'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS,
            'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE,
            'tags' => array('T1', 'T2'),
        )));

        $this->assertEquals(2, $taskRecurrenceModel->duplicateRecurringTask(1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PROCESSED, $task['recurrence_status']);
        $this->assertEquals(2, $task['recurrence_child']);
        $this->assertEquals(1436561776, $task['date_due'], '', 2);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PENDING, $task['recurrence_status']);
        $this->assertEquals(TaskModel::RECURRING_TRIGGER_CLOSE, $task['recurrence_trigger']);
        $this->assertEquals(TaskModel::RECURRING_TIMEFRAME_DAYS, $task['recurrence_timeframe']);
        $this->assertEquals(TaskModel::RECURRING_BASEDATE_TRIGGERDATE, $task['recurrence_basedate']);
        $this->assertEquals(1, $task['recurrence_parent']);
        $this->assertEquals(2, $task['recurrence_factor']);
        $this->assertEquals(strtotime('+2 days'), $task['date_due'], '', 2);

        $tags = $taskTagModel->getList(2);
        $this->assertCount(2, $tags);
        $this->assertArrayHasKey(1, $tags);
        $this->assertArrayHasKey(2, $tags);
    }
}
