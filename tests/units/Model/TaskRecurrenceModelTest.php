<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\DateParser;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskRecurrenceModel;

class TaskRecurrenceModelTest extends Base
{
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
        )));

        $this->assertEquals(2, $taskRecurrenceModel->duplicateRecurringTask(1));

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PROCESSED, $task['recurrence_status']);
        $this->assertEquals(2, $task['recurrence_child']);
        $this->assertEquals(1436486400, $task['date_due'], '', 2);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PENDING, $task['recurrence_status']);
        $this->assertEquals(TaskModel::RECURRING_TRIGGER_CLOSE, $task['recurrence_trigger']);
        $this->assertEquals(TaskModel::RECURRING_TIMEFRAME_DAYS, $task['recurrence_timeframe']);
        $this->assertEquals(TaskModel::RECURRING_BASEDATE_TRIGGERDATE, $task['recurrence_basedate']);
        $this->assertEquals(1, $task['recurrence_parent']);
        $this->assertEquals(2, $task['recurrence_factor']);
        $this->assertEquals($dateParser->removeTimeFromTimestamp(strtotime('+2 days')), $task['date_due'], '', 2);
    }
}
