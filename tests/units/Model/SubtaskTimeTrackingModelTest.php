<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\SubtaskTimeTrackingModel;
use Kanboard\Model\ProjectModel;

class SubtaskTimeTrackingModelTest extends Base
{
    public function testToggleTimer()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertFalse($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_TODO));
        $this->assertTrue($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_INPROGRESS));
        $this->assertTrue($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_DONE));
    }

    public function testToggleTimerWhenFeatureDisabled()
    {
        $configModel = new ConfigModel($this->container);
        $configModel->save(array('subtask_time_tracking' => '0'));
        $this->container['memoryCache']->flush();

        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertFalse($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_TODO));
        $this->assertFalse($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_INPROGRESS));
        $this->assertFalse($subtaskTimeTrackingModel->toggleTimer(1, 1, SubtaskModel::STATUS_DONE));
    }

    public function testHasTimer()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertFalse($subtaskTimeTrackingModel->hasTimer(1, 1));
        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(1, 1));
        $this->assertTrue($subtaskTimeTrackingModel->hasTimer(1, 1));
        $this->assertFalse($subtaskTimeTrackingModel->logStartTime(1, 1));
        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(1, 1));
        $this->assertFalse($subtaskTimeTrackingModel->hasTimer(1, 1));
    }

    public function testGetTimerStatus()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $_SESSION['user'] = array('id' => 1);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'user_id' => 1)));

        // Nothing started
        $subtasks = $subtaskModel->getAll(1);
        $this->assertNotEmpty($subtasks);
        $this->assertEquals(0, $subtasks[0]['timer_start_date']);
        $this->assertFalse($subtasks[0]['is_timer_started']);

        $subtask = $subtaskModel->getByIdWithDetails(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(0, $subtask['timer_start_date']);
        $this->assertFalse($subtask['is_timer_started']);

        // Start the clock
        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(1, 1));

        $subtasks = $subtaskModel->getAll(1);
        $this->assertNotEmpty($subtasks);
        $this->assertEquals(time(), $subtasks[0]['timer_start_date'], '', 3);
        $this->assertTrue($subtasks[0]['is_timer_started']);

        $subtask = $subtaskModel->getByIdWithDetails(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(time(), $subtask['timer_start_date'], '', 3);
        $this->assertTrue($subtask['is_timer_started']);

        // Stop the clock
        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(1, 1));
        $subtasks = $subtaskModel->getAll(1);
        $this->assertNotEmpty($subtasks);
        $this->assertEquals(0, $subtasks[0]['timer_start_date']);
        $this->assertFalse($subtasks[0]['is_timer_started']);

        $subtask = $subtaskModel->getByIdWithDetails(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(0, $subtask['timer_start_date']);
        $this->assertFalse($subtask['is_timer_started']);
    }

    public function testLogStartTime()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(1, 1));

        $timesheet = $subtaskTimeTrackingModel->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(1, $timesheet);
        $this->assertNotEmpty($timesheet[0]['start']);
        $this->assertEmpty($timesheet[0]['end']);
        $this->assertEquals(1, $timesheet[0]['user_id']);
        $this->assertEquals(1, $timesheet[0]['subtask_id']);
    }

    public function testLogStartEnd()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        // No start time
        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(1, 1));
        $timesheet = $subtaskTimeTrackingModel->getUserTimesheet(1);
        $this->assertEmpty($timesheet);

        // Log start and end time
        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(1, 1));
        sleep(1);
        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(1, 1));

        $timesheet = $subtaskTimeTrackingModel->getUserTimesheet(1);
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
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 2.2, 'time_estimated' => 3.3)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 1.1, 'time_estimated' => 4.4)));

        $time = $subtaskTimeTrackingModel->calculateSubtaskTime(1);
        $this->assertCount(2, $time);
        $this->assertEquals(3.3, $time['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(7.7, $time['time_estimated'], 'Total estimated', 0.01);
    }

    public function testUpdateSubtaskTimeSpent()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_spent' => 2.2)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(1, 1));
        $this->assertTrue($subtaskTimeTrackingModel->logStartTime(2, 1));

        // Fake start time
        $this->container['db']->table(SubtaskTimeTrackingModel::TABLE)->update(array('start' => time() - 3600));

        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(1, 1));
        $this->assertTrue($subtaskTimeTrackingModel->logEndTime(2, 1));

        $timesheet = $subtaskTimeTrackingModel->getUserTimesheet(1);
        $this->assertNotEmpty($timesheet);
        $this->assertCount(2, $timesheet);
        $this->assertEquals(3600, $timesheet[0]['end'] - $timesheet[0]['start'], 'Wrong timestamps', 1);
        $this->assertEquals(3600, $timesheet[1]['end'] - $timesheet[1]['start'], 'Wrong timestamps', 1);

        $time = $subtaskTimeTrackingModel->calculateSubtaskTime(1);
        $this->assertEquals(4.2, $time['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(0, $time['time_estimated'], 'Total estimated', 0.01);

        $time = $subtaskTimeTrackingModel->calculateSubtaskTime(2);
        $this->assertEquals(0, $time['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(0, $time['time_estimated'], 'Total estimated', 0.01);
    }

    public function testUpdateTaskTimeTracking()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'test 2', 'project_id' => 1, 'time_estimated' => 1.5, 'time_spent' => 0.5)));
        $this->assertEquals(3, $taskCreationModel->create(array('title' => 'test 3', 'project_id' => 1, 'time_estimated' => 4, 'time_spent' => 2)));

        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_spent' => 2.2)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => 1)));

        $this->assertEquals(3, $subtaskModel->create(array('title' => 'subtask #3', 'task_id' => 2, 'time_spent' => 3.4)));
        $this->assertEquals(4, $subtaskModel->create(array('title' => 'subtask #4', 'task_id' => 2, 'time_estimated' => 1.25)));

        $this->assertEquals(5, $subtaskModel->create(array('title' => 'subtask #5', 'task_id' => 3, 'time_spent' => 8)));

        $subtaskTimeTrackingModel->updateTaskTimeTracking(1);
        $subtaskTimeTrackingModel->updateTaskTimeTracking(2);
        $subtaskTimeTrackingModel->updateTaskTimeTracking(3);

        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2.2, $task['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(1, $task['time_estimated'], 'Total estimated', 0.01);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(3.4, $task['time_spent'], 'Total spent', 0.01);
        $this->assertEquals(1.25, $task['time_estimated'], 'Total estimated', 0.01);

        $task = $taskFinderModel->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(8, $task['time_spent']);

        $this->assertTrue($subtaskModel->remove(3));
        $this->assertTrue($subtaskModel->remove(4));

        $subtaskTimeTrackingModel->updateTaskTimeTracking(2);

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['time_estimated']);
        $this->assertEquals(0, $task['time_spent']);
    }
}
