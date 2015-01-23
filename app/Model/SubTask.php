<?php

namespace Model;

use Event\SubtaskEvent;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Subtask model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class SubTask extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_subtasks';

    /**
     * Task "done" status
     *
     * @var integer
     */
    const STATUS_DONE = 2;

    /**
     * Task "in progress" status
     *
     * @var integer
     */
    const STATUS_INPROGRESS = 1;

    /**
     * Task "todo" status
     *
     * @var integer
     */
    const STATUS_TODO = 0;

    /**
     * Events
     *
     * @var string
     */
    const EVENT_UPDATE = 'subtask.update';
    const EVENT_CREATE = 'subtask.create';

    /**
     * Get available status
     *
     * @access public
     * @return array
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
        }

        return $subtasks;
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
        return $this->db->table(SubTask::TABLE)
            ->columns(
                SubTask::TABLE.'.*',
                Task::TABLE.'.project_id',
                Task::TABLE.'.color_id',
                Project::TABLE.'.name AS project_name'
            )
            ->eq('user_id', $user_id)
            ->in(SubTask::TABLE.'.status', $status)
            ->join(Task::TABLE, 'id', 'task_id')
            ->join(Project::TABLE, 'id', 'project_id', Task::TABLE)
            ->filter(array($this, 'addStatusName'));
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
                    ->columns(self::TABLE.'.*', User::TABLE.'.username', User::TABLE.'.name')
                    ->join(User::TABLE, 'id', 'user_id')
                    ->asc(self::TABLE.'.id')
                    ->filter(array($this, 'addStatusName'))
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
                        ->columns(self::TABLE.'.*', User::TABLE.'.username', User::TABLE.'.name')
                        ->join(User::TABLE, 'id', 'user_id')
                        ->filter(array($this, 'addStatusName'))
                        ->findOne();
        }

        return $this->db->table(self::TABLE)->eq('id', $subtask_id)->findOne();
    }

    /**
     * Prepare data before insert/update
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        $this->removeFields($values, array('another_subtask'));
        $this->resetFields($values, array('time_estimated', 'time_spent'));
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
        $this->prepare($values);
        $subtask_id = $this->persist(self::TABLE, $values);

        if ($subtask_id) {
            $this->container['dispatcher']->dispatch(
                self::EVENT_CREATE,
                new SubtaskEvent(array('id' => $subtask_id) + $values)
            );
        }

        return $subtask_id;
    }

    /**
     * Update
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        $this->prepare($values);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);

        if ($result) {
            $this->container['dispatcher']->dispatch(
                self::EVENT_UPDATE,
                new SubtaskEvent($values)
            );
        }

        return $result;
    }

    /**
     * Change the status of subtask
     *
     * Todo -> In progress -> Done -> Todo -> etc...
     *
     * @access public
     * @param  integer  $subtask_id
     * @return bool
     */
    public function toggleStatus($subtask_id)
    {
        $subtask = $this->getById($subtask_id);

        $values = array(
            'id' => $subtask['id'],
            'status' => ($subtask['status'] + 1) % 3,
            'task_id' => $subtask['task_id'],
        );

        return $this->update($values);
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
        return $this->db->transaction(function ($db) use ($src_task_id, $dst_task_id) {

            $subtasks = $db->table(SubTask::TABLE)
                                 ->columns('title', 'time_estimated')
                                 ->eq('task_id', $src_task_id)
                                 ->asc('id') // Explicit sorting for postgresql
                                 ->findAll();

            foreach ($subtasks as &$subtask) {

                $subtask['task_id'] = $dst_task_id;

                if (! $db->table(SubTask::TABLE)->save($subtask)) {
                    return false;
                }
            }
        });
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate API modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateApiModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Integer('id', t('The subtask id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 100), 100),
            new Validators\Integer('user_id', t('The user id must be an integer')),
            new Validators\Integer('status', t('The status must be an integer')),
            new Validators\Numeric('time_estimated', t('The time must be a numeric value')),
            new Validators\Numeric('time_spent', t('The time must be a numeric value')),
        );
    }
}
