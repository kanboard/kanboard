<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ConfigModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\SubtaskTimeTrackingModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;

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

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')), 1);
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
        $this->assertEqualsWithDelta(time(), $subtasks[0]['timer_start_date'], 3, '');
        $this->assertTrue($subtasks[0]['is_timer_started']);

        $subtask = $subtaskModel->getByIdWithDetails(1);
        $this->assertNotEmpty($subtask);
        $this->assertEqualsWithDelta(time(), $subtask['timer_start_date'], 3, '');
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
        $this->assertEqualsWithDelta(3.3, $time['time_spent'], 0.01, 'Total spent');
        $this->assertEqualsWithDelta(7.7, $time['time_estimated'], 0.01, 'Total estimated');
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
        $this->assertEqualsWithDelta(3600, $timesheet[0]['end'] - $timesheet[0]['start'], 1, 'Wrong timestamps');
        $this->assertEqualsWithDelta(3600, $timesheet[1]['end'] - $timesheet[1]['start'], 1, 'Wrong timestamps');

        $time = $subtaskTimeTrackingModel->calculateSubtaskTime(1);
        $this->assertEqualsWithDelta(4.2, $time['time_spent'], 0.01, 'Total spent');
        $this->assertEqualsWithDelta(0, $time['time_estimated'], 0.01, 'Total estimated');

        $time = $subtaskTimeTrackingModel->calculateSubtaskTime(2);
        $this->assertEqualsWithDelta(0, $time['time_spent'], 0.01, 'Total spent');
        $this->assertEqualsWithDelta(0, $time['time_estimated'], 0.01, 'Total estimated');
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
        $this->assertEqualsWithDelta(2.2, $task['time_spent'], 0.01, 'Total spent');
        $this->assertEqualsWithDelta(1, $task['time_estimated'], 0.01, 'Total estimated');

        $task = $taskFinderModel->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEqualsWithDelta(3.4, $task['time_spent'], 0.01, 'Total spent');
        $this->assertEqualsWithDelta(1.25, $task['time_estimated'], 0.01, 'Total estimated');

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

    public function testUserTaskQueries()
    {
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $subtaskTimeTrackingModel = new SubtaskTimeTrackingModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, ($userModel->create(array('username' => 'user #2', 'name' => 'fn2'))));
        $this->assertEquals(3, ($userModel->create(array('username' => 'user #3', 'name' => 'fn3'))));
        $this->assertEquals(1, $projectModel->create(array('name' => 'project #1')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'task #1.1', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1.1.1', 'task_id' => 1,)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #1.1.2', 'task_id' => 1,)));

        $subtaskTimeTrackingModel->logStartTime(1, 1);
        $subtaskTimeTrackingModel->logStartTime(1, 3);
        $subtaskTimeTrackingModel->logStartTime(2, 3);
        sleep(1);
        $subtaskTimeTrackingModel->logStartTime(2, 2);
        $subtaskTimeTrackingModel->logEndTime(1, 1);
        sleep(2);
        $subtaskTimeTrackingModel->logEndTime(1, 3);
        sleep(3);
        $subtaskTimeTrackingModel->logEndTime(2, 2);
        $subtaskTimeTrackingModel->logEndTime(2, 3);

        $u1stt = $subtaskTimeTrackingModel->getUserQuery(1)->findAll();
        $this->assertCount(1, $u1stt, "User #1 SubTaskTiming Query Row Count");
        $row = $u1stt[0];
        $this->assertCount(10, $row, "User #1 SubTaskTiming Query Column Count");
        $this->assertEquals(1, $row['id'], "User #1 SubTaskTiming Query Id");
        $this->assertEquals(1, $row['subtask_id'], "User #1 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(1, $row['end'] - $row['start'], 1, "User #1 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "User #1 SubTaskTiming Query TimeSpent");
        $this->assertEquals(1, $row['task_id'], "User #1 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.1', $row['subtask_title'], "User #1 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals('task #1.1', $row['task_title'], "User #1 SubTaskTiming Query TaskTitle");
        $this->assertEquals(1, $row['project_id'], "User #1 SubTaskTiming Query ProjectId");
        $this->assertEquals('yellow', $row['color_id'], "User #1 SubTaskTiming Query Color");

        $u2stt = $subtaskTimeTrackingModel->getUserQuery(2)->findAll();
        $this->assertCount(1, $u2stt, "User #2 SubTaskTiming Query Count");
        $row = $u2stt[0];
        $this->assertCount(10, $row, "User #2 SubTaskTiming Query Column Count");
        $this->assertEquals(4, $row['id'], "User #2 SubTaskTiming Query Id");
        $this->assertEquals(2, $row['subtask_id'], "User #2 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(5, $row['end'] - $row['start'], 1, "User #2 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "User #2 SubTaskTiming Query TimeSpent");
        $this->assertEquals(1, $row['task_id'], "User #2 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.2', $row['subtask_title'], "User #2 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals('task #1.1', $row['task_title'], "User #2 SubTaskTiming Query TaskTitle");
        $this->assertEquals(1, $row['project_id'], "User #2 SubTaskTiming Query ProjectId");
        $this->assertEquals('yellow', $row['color_id'], "User #2 SubTaskTiming Query Color");

        $u3stt = $subtaskTimeTrackingModel->getUserQuery(3)->orderBy(
                 $subtaskTimeTrackingModel->db->escapeIdentifier('id', SubtaskTimeTrackingModel::TABLE)
        )->findAll();
        $this->assertCount(2, $u3stt, "User #3 SubTaskTiming Count");
        $row = $u3stt[0];
        $this->assertCount(10, $row, "User #3 SubTaskTiming Query Column Count");
        $this->assertEquals(2, $row['id'], "User #3 SubTaskTiming Query Id");
        $this->assertEquals(1, $row['subtask_id'], "User #3 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(3, $row['end'] - $row['start'], 1, "User #3 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "User #3 SubTaskTiming Query TimeSpent");
        $this->assertEquals(1, $row['task_id'], "User #3 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.1', $row['subtask_title'], "User #3 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals('task #1.1', $row['task_title'], "User #3 SubTaskTiming Query TaskTitle");
        $this->assertEquals(1, $row['project_id'], "User #3 SubTaskTiming Query ProjectId");
        $this->assertEquals('yellow', $row['color_id'], "User #3 SubTaskTiming Query Color");
        $row = $u3stt[1];
        $this->assertCount(10, $row, "User #3 SubTaskTiming Query Column Count");
        $this->assertEquals(3, $row['id'], "User #3 SubTaskTiming Query Id");
        $this->assertEquals(2, $row['subtask_id'], "User #3 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(6, $row['end'] - $row['start'], 1, "User #3 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "User #3 SubTaskTiming Query TimeSpent");
        $this->assertEquals(1, $row['task_id'], "User #3 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.2', $row['subtask_title'], "User #3 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals('task #1.1', $row['task_title'], "User #3 SubTaskTiming Query TaskTitle");
        $this->assertEquals(1, $row['project_id'], "User #3 SubTaskTiming Query ProjectId");
        $this->assertEquals('yellow', $row['color_id'], "User #3 SubTaskTiming Query Color");

        $t1stt = $subtaskTimeTrackingModel->getTaskQuery(1)->orderBy(
                 $subtaskTimeTrackingModel->db->escapeIdentifier('id', SubtaskTimeTrackingModel::TABLE)
        )->findAll();
        $this->assertCount(4, $t1stt, "Task #1 SubTaskTiming Count");
        $row = $t1stt[0];
        $this->assertCount(11, $row, "Task #1 SubTaskTiming Query Column Count");
        $this->assertEquals(1, $row['id'], "Task #1 SubTaskTiming Query Id");
        $this->assertEquals(1, $row['subtask_id'], "Task #1 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(1, $row['end'] - $row['start'], 1, "Task #1 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "Task #1 SubTaskTiming Query TimeSpent");
        $this->assertEquals(1, $row['user_id'], "Task #1 SubTaskTiming Query UserId");
        $this->assertEquals(1, $row['task_id'], "Task #1 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.1', $row['subtask_title'], "Task #1 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals(1, $row['project_id'], "Task #1 SubTaskTiming Query ProjectId");
        $this->assertEquals('admin', $row['username'], "Task #1 SubTaskTiming Query UserName");
        $this->assertEquals(NULL, $row['user_fullname'], "Task #1 SubTaskTiming Query UserFullName");
        $row = $t1stt[1];
        $this->assertCount(11, $row, "Task #1 SubTaskTiming Query Column Count");
        $this->assertEquals(2, $row['id'], "Task #1 SubTaskTiming Query Id");
        $this->assertEquals(1, $row['subtask_id'], "Task #1 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(3, $row['end'] - $row['start'], 1, "Task #1 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "Task #1 SubTaskTiming Query TimeSpent");
        $this->assertEquals(3, $row['user_id'], "Task #1 SubTaskTiming Query UserId");
        $this->assertEquals(1, $row['task_id'], "Task #1 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.1', $row['subtask_title'], "Task #1 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals(1, $row['project_id'], "Task #1 SubTaskTiming Query ProjectId");
        $this->assertEquals('user #3', $row['username'], "Task #1 SubTaskTiming Query UserName");
        $this->assertEquals('fn3', $row['user_fullname'], "Task #1 SubTaskTiming Query UserFullName");
        $row = $t1stt[2];
        $this->assertCount(11, $row, "Task #1 SubTaskTiming Query Column Count");
        $this->assertEquals(3, $row['id'], "Task #1 SubTaskTiming Query Id");
        $this->assertEquals(2, $row['subtask_id'], "Task #1 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(6, $row['end'] - $row['start'], 1, "Task #1 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "Task #1 SubTaskTiming Query TimeSpent");
        $this->assertEquals(3, $row['user_id'], "Task #1 SubTaskTiming Query UserId");
        $this->assertEquals(1, $row['task_id'], "Task #1 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.2', $row['subtask_title'], "Task #1 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals(1, $row['project_id'], "Task #1 SubTaskTiming Query ProjectId");
        $this->assertEquals('user #3', $row['username'], "Task #1 SubTaskTiming Query UserName");
        $this->assertEquals('fn3', $row['user_fullname'], "Task #1 SubTaskTiming Query UserFullName");
        $row = $t1stt[3];
        $this->assertCount(11, $row, "Task #1 SubTaskTiming Query Column Count");
        $this->assertEquals(4, $row['id'], "Task #1 SubTaskTiming Query Id");
        $this->assertEquals(2, $row['subtask_id'], "Task #1 SubTaskTiming Query SubtaskId");
        $this->assertEqualsWithDelta(5, $row['end'] - $row['start'], 1, "Task #1 SubTaskTiming Query Timing");
        $this->assertEquals(0, $row['time_spent'], "Task #1 SubTaskTiming Query TimeSpent");
        $this->assertEquals(2, $row['user_id'], "Task #1 SubTaskTiming Query UserId");
        $this->assertEquals(1, $row['task_id'], "Task #1 SubTaskTiming Query TaskId");
        $this->assertEquals('subtask #1.1.2', $row['subtask_title'], "Task #1 SubTaskTiming Query SubtaskTitle");
        $this->assertEquals(1, $row['project_id'], "Task #1 SubTaskTiming Query ProjectId");
        $this->assertEquals('user #2', $row['username'], "Task #1 SubTaskTiming Query UserName");
        $this->assertEquals('fn2', $row['user_fullname'], "Task #1 SubTaskTiming Query UserFullName");
    }
}
