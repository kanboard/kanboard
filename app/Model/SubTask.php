<?php

namespace Model;

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
     * Get available status
     *
     * @access public
     * @return array
     */
    public function getStatusList()
    {
        $status = array(
            self::STATUS_TODO => t('Todo'),
            self::STATUS_INPROGRESS => t('In progress'),
            self::STATUS_DONE => t('Done'),
        );

        asort($status);

        return $status;
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
        $status = $this->getStatusList();
        $subtasks = $this->db->table(self::TABLE)
                             ->eq('task_id', $task_id)
                             ->columns(self::TABLE.'.*', User::TABLE.'.username')
                             ->join(User::TABLE, 'id', 'user_id')
                             ->findAll();

        foreach ($subtasks as &$subtask) {
            $subtask['status_name'] = $status[$subtask['status']];
        }

        return $subtasks;
    }

    /**
     * Get a subtask by the id
     *
     * @access public
     * @param  integer   $subtask_id    Subtask id
     * @return array
     */
    public function getById($subtask_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $subtask_id)->findOne();
    }

    /**
     * Create
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function create(array $values)
    {
        if (isset($values['another_subtask'])) {
            unset($values['another_subtask']);
        }

        if (isset($values['time_estimated']) && empty($values['time_estimated'])) {
            $values['time_estimated'] = 0;
        }

        if (isset($values['time_spent']) && empty($values['time_spent'])) {
            $values['time_spent'] = 0;
        }

        return $this->db->table(self::TABLE)->save($values);
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
        if (isset($values['time_estimated']) && empty($values['time_estimated'])) {
            $values['time_estimated'] = 0;
        }

        if (isset($values['time_spent']) && empty($values['time_spent'])) {
            $values['time_spent'] = 0;
        }

        return $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
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
     * Validate creation/modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validate(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 100), 100),
            new Validators\Integer('user_id', t('The user id must be an integer')),
            new Validators\Integer('status', t('The status must be an integer')),
            new Validators\Numeric('time_estimated', t('The time must be a numeric value')),
            new Validators\Numeric('time_spent', t('The time must be a numeric value')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
