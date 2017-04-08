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
     * @param  integer $subtaskId
     * @return integer
     */
    public function getProjectId($subtaskId)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $subtaskId)
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
            self::STATUS_TODO       => t('Todo'),
            self::STATUS_INPROGRESS => t('In progress'),
            self::STATUS_DONE       => t('Done'),
        );
    }

    /**
     * Get common query
     *
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.*',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name'
            )
            ->subquery($this->subtaskTimeTrackingModel->getTimerQuery($this->userSession->getId()), 'timer_start_date')
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->asc(self::TABLE.'.position');
    }

    public function countByAssigneeAndTaskStatus($userId)
    {
        return $this->db->table(self::TABLE)
            ->eq('user_id', $userId)
            ->eq(TaskModel::TABLE.'.is_active', TaskModel::STATUS_OPEN)
            ->join(Taskmodel::TABLE, 'id', 'task_id')
            ->count();
    }

    /**
     * Get all subtasks for a given task
     *
     * @access public
     * @param  integer   $taskId
     * @return array
     */
    public function getAll($taskId)
    {
        return $this->subtaskListFormatter
            ->withQuery($this->getQuery()->eq('task_id', $taskId))
            ->format();
    }

    /**
     * Get subtasks for a list of tasks
     *
     * @param array $taskIds
     * @return array
     */
    public function getAllByTaskIds(array $taskIds)
    {
        if (empty($taskIds)) {
            return array();
        }

        return $this->subtaskListFormatter
            ->withQuery($this->getQuery()->in('task_id', $taskIds))
            ->format();
    }

    /**
     * Get subtasks for a list of tasks and a given assignee
     *
     * @param  array   $taskIds
     * @param  integer $userId
     * @return array
     */
    public function getAllByTaskIdsAndAssignee(array $taskIds, $userId)
    {
        if (empty($taskIds)) {
            return array();
        }

        return $this->subtaskListFormatter
            ->withQuery($this->getQuery()->in('task_id', $taskIds)->eq(self::TABLE.'.user_id', $userId))
            ->format();
    }

    /**
     * Get a subtask by the id
     *
     * @access public
     * @param  integer   $subtaskId
     * @return array
     */
    public function getById($subtaskId)
    {
        return $this->db->table(self::TABLE)->eq('id', $subtaskId)->findOne();
    }

    /**
     * Get subtask with additional information
     *
     * @param  integer $subtaskId
     * @return array|null
     */
    public function getByIdWithDetails($subtaskId)
    {
        $subtasks = $this->subtaskListFormatter
            ->withQuery($this->getQuery()->eq(self::TABLE.'.id', $subtaskId))
            ->format();

        if (! empty($subtasks)) {
            return $subtasks[0];
        }

        return null;
    }

    /**
     * Get the position of the last column for a given project
     *
     * @access public
     * @param  integer  $taskId
     * @return integer
     */
    public function getLastPosition($taskId)
    {
        return (int) $this->db
                        ->table(self::TABLE)
                        ->eq('task_id', $taskId)
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
        $subtaskId = $this->db->table(self::TABLE)->persist($values);

        if ($subtaskId !== false) {
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($values['task_id']);
            $this->queueManager->push($this->subtaskEventJob->withParams($subtaskId, self::EVENT_CREATE));
        }

        return $subtaskId;
    }

    /**
     * Update a subtask
     *
     * @access public
     * @param  array $values
     * @param  bool  $fireEvent
     * @return bool
     */
    public function update(array $values, $fireEvent = true)
    {
        $this->prepare($values);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);

        if ($result) {
            $subtask = $this->getById($values['id']);
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($subtask['task_id']);

            if ($fireEvent) {
                $this->queueManager->push($this->subtaskEventJob->withParams($subtask['id'], self::EVENT_UPDATE, $values));
            }
        }

        return $result;
    }

    /**
     * Remove
     *
     * @access public
     * @param  integer $subtaskId
     * @return bool
     */
    public function remove($subtaskId)
    {
        $this->subtaskEventJob->execute($subtaskId, self::EVENT_DELETE);
        return $this->db->table(self::TABLE)->eq('id', $subtaskId)->remove();
    }

    /**
     * Duplicate all subtasks to another task
     *
     * @access public
     * @param  integer $srcTaskId
     * @param  integer $dstTaskId
     * @return bool
     */
    public function duplicate($srcTaskId, $dstTaskId)
    {
        return $this->db->transaction(function (Database $db) use ($srcTaskId, $dstTaskId) {

            $subtasks = $db->table(SubtaskModel::TABLE)
                ->columns('title', 'time_estimated', 'position')
                ->eq('task_id', $srcTaskId)
                ->asc('position')
                ->findAll();

            foreach ($subtasks as &$subtask) {
                $subtask['task_id'] = $dstTaskId;

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
}
