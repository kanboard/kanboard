<?php

namespace Kanboard\Model;

use PicoDb\Database;
use Kanboard\Core\Base;

/**
 * Subtask Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class SubtaskModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'subtasks';

    /**
     * Subtask status
     *
     * @var integer
     */
    const STATUS_TODO = 0;
    const STATUS_INPROGRESS = 1;
    const STATUS_DONE = 2;

    /**
     * Events
     *
     * @var string
     */
    const EVENT_UPDATE = 'subtask.update';
    const EVENT_CREATE = 'subtask.create';
    const EVENT_DELETE = 'subtask.delete';

    /**
     * Get projectId from subtaskId
     *
     * @access public
     * @param  integer $subtask_id
     * @return integer
     */
    public function getProjectId($subtask_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $subtask_id)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->findOneColumn(TaskModel::TABLE . '.project_id') ?: 0;
    }

    /**
     * Get available status
     *
     * @access public
     * @return string[]
     */
    public function getStatusList()
    {
        return array(
            self::STATUS_TODO => t('Todo'),
            self::STATUS_INPROGRESS => t('In progress'),
            self::STATUS_DONE => t('Done'),
        );
    }

    /**
     * Get the query to fetch subtasks assigned to a user
     *
     * @access public
     * @param  integer    $user_id         User id
     * @param  array      $status          List of status
     * @return \PicoDb\Table
     */
    public function getUserQuery($user_id, array $status)
    {
        return $this->db->table(SubtaskModel::TABLE)
            ->columns(
                SubtaskModel::TABLE.'.*',
                TaskModel::TABLE.'.project_id',
                TaskModel::TABLE.'.color_id',
                TaskModel::TABLE.'.title AS task_name',
                ProjectModel::TABLE.'.name AS project_name'
            )
            ->subquery($this->subtaskTimeTrackingModel->getTimerQuery($user_id), 'timer_start_date')
            ->eq('user_id', $user_id)
            ->eq(ProjectModel::TABLE.'.is_active', ProjectModel::ACTIVE)
            ->in(SubtaskModel::TABLE.'.status', $status)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
            ->callback(array($this, 'addStatusName'));
    }

    /**
     * Get all subtasks for a given task
     *
     * @access public
     * @param  integer   $task_id    Task id
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('task_id', $task_id)
                    ->columns(
                        self::TABLE.'.*',
                        UserModel::TABLE.'.username',
                        UserModel::TABLE.'.name'
                    )
                    ->subquery($this->subtaskTimeTrackingModel->getTimerQuery($this->userSession->getId()), 'timer_start_date')
                    ->join(UserModel::TABLE, 'id', 'user_id')
                    ->asc(self::TABLE.'.position')
                    ->callback(array($this, 'addStatusName'))
                    ->findAll();
    }

    /**
     * Get a subtask by the id
     *
     * @access public
     * @param  integer   $subtask_id    Subtask id
     * @param  bool      $more          Fetch more data
     * @return array
     */
    public function getById($subtask_id, $more = false)
    {
        if ($more) {
            return $this->db
                        ->table(self::TABLE)
                        ->eq(self::TABLE.'.id', $subtask_id)
                        ->columns(self::TABLE.'.*', UserModel::TABLE.'.username', UserModel::TABLE.'.name')
                        ->subquery($this->subtaskTimeTrackingModel->getTimerQuery($this->userSession->getId()), 'timer_start_date')
                        ->join(UserModel::TABLE, 'id', 'user_id')
                        ->callback(array($this, 'addStatusName'))
                        ->findOne();
        }

        return $this->db->table(self::TABLE)->eq('id', $subtask_id)->findOne();
    }

    /**
     * Get the position of the last column for a given project
     *
     * @access public
     * @param  integer  $task_id   Task id
     * @return integer
     */
    public function getLastPosition($task_id)
    {
        return (int) $this->db
                        ->table(self::TABLE)
                        ->eq('task_id', $task_id)
                        ->desc('position')
                        ->findOneColumn('position');
    }

    /**
     * Create a new subtask
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        $this->prepareCreation($values);
        $subtask_id = $this->db->table(self::TABLE)->persist($values);

        if ($subtask_id !== false) {
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($values['task_id']);
            $this->queueManager->push($this->subtaskEventJob->withParams($subtask_id, self::EVENT_CREATE));
        }

        return $subtask_id;
    }

    /**
     * Update
     *
     * @access public
     * @param  array $values
     * @param  bool  $fire_event
     * @return bool
     */
    public function update(array $values, $fire_event = true)
    {
        $this->prepare($values);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);

        if ($result) {
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($values['task_id']);

            if ($fire_event) {
                $this->queueManager->push($this->subtaskEventJob->withParams($values['id'], self::EVENT_UPDATE, $values));
            }
        }

        return $result;
    }

    /**
     * Remove
     *
     * @access public
     * @param  integer   $subtask_id    Subtask id
     * @return bool
     */
    public function remove($subtask_id)
    {
        $this->subtaskEventJob->execute($subtask_id, self::EVENT_DELETE);
        return $this->db->table(self::TABLE)->eq('id', $subtask_id)->remove();
    }

    /**
     * Duplicate all subtasks to another task
     *
     * @access public
     * @param  integer   $src_task_id    Source task id
     * @param  integer   $dst_task_id    Destination task id
     * @return bool
     */
    public function duplicate($src_task_id, $dst_task_id)
    {
        return $this->db->transaction(function (Database $db) use ($src_task_id, $dst_task_id) {

            $subtasks = $db->table(SubtaskModel::TABLE)
                ->columns('title', 'time_estimated', 'position')
                ->eq('task_id', $src_task_id)
                ->asc('position')
                ->findAll();

            foreach ($subtasks as &$subtask) {
                $subtask['task_id'] = $dst_task_id;

                if (! $db->table(SubtaskModel::TABLE)->save($subtask)) {
                    return false;
                }
            }
        });
    }

    /**
     * Prepare data before insert/update
     *
     * @access protected
     * @param  array    $values    Form values
     */
    protected function prepare(array &$values)
    {
        $this->helper->model->removeFields($values, array('another_subtask'));
        $this->helper->model->resetFields($values, array('time_estimated', 'time_spent'));
        $this->hook->reference('model:subtask:modification:prepare', $values);
    }

    /**
     * Prepare data before insert
     *
     * @access protected
     * @param  array    $values    Form values
     */
    protected function prepareCreation(array &$values)
    {
        $this->prepare($values);

        $values['position'] = $this->getLastPosition($values['task_id']) + 1;
        $values['status'] = isset($values['status']) ? $values['status'] : self::STATUS_TODO;
        $values['time_estimated'] = isset($values['time_estimated']) ? $values['time_estimated'] : 0;
        $values['time_spent'] = isset($values['time_spent']) ? $values['time_spent'] : 0;
        $values['user_id'] = isset($values['user_id']) ? $values['user_id'] : 0;
        $this->hook->reference('model:subtask:creation:prepare', $values);
    }

    /**
     * Add subtask status status to the resultset
     *
     * @access public
     * @param  array    $subtasks   Subtasks
     * @return array
     */
    public function addStatusName(array $subtasks)
    {
        $status = $this->getStatusList();

        foreach ($subtasks as &$subtask) {
            $subtask['status_name'] = $status[$subtask['status']];
            $subtask['timer_start_date'] = isset($subtask['timer_start_date']) ? $subtask['timer_start_date'] : 0;
            $subtask['is_timer_started'] = ! empty($subtask['timer_start_date']);
        }

        return $subtasks;
    }
}
