<?php

namespace Model;

require_once __DIR__.'/base.php';
require_once __DIR__.'/comment.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

/**
 * Task model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Task extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE               = 'tasks';

    /**
     * Task status
     *
     * @var integer
     */
    const STATUS_OPEN         = 1;
    const STATUS_CLOSED       = 0;

    /**
     * Events
     *
     * @var string
     */
    const EVENT_MOVE_COLUMN   = 'task.move.column';
    const EVENT_MOVE_POSITION = 'task.move.position';
    const EVENT_UPDATE        = 'task.update';
    const EVENT_CREATE        = 'task.create';
    const EVENT_CLOSE         = 'task.close';
    const EVENT_OPEN          = 'task.open';

    /**
     * Get available colors
     *
     * @access public
     * @return array
     */
    public function getColors()
    {
        return array(
            'yellow' => t('Yellow'),
            'blue' => t('Blue'),
            'green' => t('Green'),
            'purple' => t('Purple'),
            'red' => t('Red'),
            'orange' => t('Orange'),
            'grey' => t('Grey'),
        );
    }

    /**
     * Fetch one task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @param  boolean   $more      If true, fetch all related information
     * @return array
     */
    public function getById($task_id, $more = false)
    {
        if ($more) {

            return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.title',
                        self::TABLE.'.description',
                        self::TABLE.'.date_creation',
                        self::TABLE.'.date_completed',
                        self::TABLE.'.date_due',
                        self::TABLE.'.color_id',
                        self::TABLE.'.project_id',
                        self::TABLE.'.column_id',
                        self::TABLE.'.owner_id',
                        self::TABLE.'.position',
                        self::TABLE.'.is_active',
                        self::TABLE.'.score',
                        Project::TABLE.'.name AS project_name',
                        Board::TABLE.'.title AS column_title',
                        User::TABLE.'.username'
                    )
                    ->join(Project::TABLE, 'id', 'project_id')
                    ->join(Board::TABLE, 'id', 'column_id')
                    ->join(User::TABLE, 'id', 'owner_id')
                    ->eq(self::TABLE.'.id', $task_id)
                    ->findOne();
        }
        else {

            return $this->db->table(self::TABLE)->eq('id', $task_id)->findOne();
        }
    }

    /**
     * Get all tasks for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return array
     */
    public function getAllByProjectId($project_id, array $status = array(self::STATUS_OPEN, self::STATUS_CLOSED))
    {
        return $this->db->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.title',
                self::TABLE.'.description',
                self::TABLE.'.date_creation',
                self::TABLE.'.date_completed',
                self::TABLE.'.date_due',
                self::TABLE.'.color_id',
                self::TABLE.'.project_id',
                self::TABLE.'.column_id',
                self::TABLE.'.owner_id',
                self::TABLE.'.position',
                self::TABLE.'.is_active',
                self::TABLE.'.score',
                Board::TABLE.'.title AS column_title',
                User::TABLE.'.username'
            )
            ->join(Board::TABLE, 'id', 'column_id')
            ->join(User::TABLE, 'id', 'owner_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->in('is_active', $status)
            ->desc('date_completed')
            ->findAll();
    }

    /**
     * Count all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return integer
     */
    public function countByProjectId($project_id, array $status = array(self::STATUS_OPEN, self::STATUS_CLOSED))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
                    ->count();
    }

    /**
     * Get all tasks for a given column
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $column_id    Column id
     * @param  array     $status       List of status id
     * @return array
     */
    public function getAllByColumnId($project_id, $column_id, array $status = array(self::STATUS_OPEN))
    {
        $tasks = $this->db
                    ->table(self::TABLE)
                    ->columns('tasks.id', 'title', 'color_id', 'project_id', 'owner_id', 'column_id', 'position', 'score', 'date_due', 'users.username')
                    ->join('users', 'id', 'owner_id')
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->asc('position')
                    ->findAll();

        $commentModel = new Comment($this->db, $this->event);

        foreach ($tasks as &$task) {
            $task['nb_comments'] = $commentModel->count($task['id']);
        }

        return $tasks;
    }

    /**
     * Count the number of tasks for a given column and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $column_id    Column id
     * @param  array     $status       List of status id
     * @return integer
     */
    public function countByColumnId($project_id, $column_id, array $status = array(self::STATUS_OPEN))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->count();
    }

    /**
     * Duplicate a task
     *
     * @access public
     * @param  integer   $task_id      Task id
     * @return boolean
     */
    public function duplicate($task_id)
    {
        $this->db->startTransaction();

        $boardModel = new Board($this->db, $this->event);

        // Get the original task
        $task = $this->getById($task_id);

        // Cleanup data
        unset($task['id']);
        unset($task['date_completed']);

        // Assign new values
        $task['date_creation'] = time();
        $task['is_active'] = 1;
        $task['position'] = $this->countByColumnId($task['project_id'], $task['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($task)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $task);

        return $task_id;
    }

    /**
     * Duplicate a task to another project (always copy to the first column)
     *
     * @access public
     * @param  integer   $task_id      Task id
     * @param  integer   $project_id   Destination project id
     * @return boolean
     */
    public function duplicateToAnotherProject($task_id, $project_id)
    {
        $this->db->startTransaction();

        $boardModel = new Board($this->db, $this->event);

        // Get the original task
        $task = $this->getById($task_id);

        // Cleanup data
        unset($task['id']);
        unset($task['date_completed']);

        // Assign new values
        $task['date_creation'] = time();
        $task['owner_id'] = 0;
        $task['is_active'] = 1;
        $task['column_id'] = $boardModel->getFirstColumn($project_id);
        $task['project_id'] = $project_id;
        $task['position'] = $this->countByColumnId($task['project_id'], $task['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($task)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $task);

        return $task_id;
    }

    /**
     * Create a task
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        // Prepare data
        if (isset($values['another_task'])) {
            unset($values['another_task']);
        }

        if (! empty($values['date_due']) && ! is_numeric($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        $values['date_creation'] = time();
        $values['position'] = $this->countByColumnId($values['project_id'], $values['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    /**
     * Update a task
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean
     */
    public function update(array $values)
    {
        // Prepare data
        if (! empty($values['date_due']) && ! is_numeric($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        $original_task = $this->getById($values['id']);

        if ($original_task === false) {
            return false;
        }

        $updated_task = $values;
        unset($updated_task['id']);

        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($updated_task);

        // Trigger events
        if ($result) {

            $events = array();

            if ($this->event->getLastTriggeredEvent() !== self::EVENT_UPDATE) {
                $events[] = self::EVENT_UPDATE;
            }

            if (isset($values['column_id']) && $original_task['column_id'] != $values['column_id']) {
                $events[] = self::EVENT_MOVE_COLUMN;
            }
            else if (isset($values['position']) && $original_task['position'] != $values['position']) {
                $events[] = self::EVENT_MOVE_POSITION;
            }

            $event_data = array_merge($original_task, $values);
            $event_data['task_id'] = $original_task['id'];

            foreach ($events as $event) {
                $this->event->trigger($event, $event_data);
            }
        }

        return $result;
    }

    /**
     * Mark a task closed
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function close($task_id)
    {
        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 0,
                            'date_completed' => time()
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_CLOSE, array('task_id' => $task_id) + $this->getById($task_id));
        }

        return $result;
    }

    /**
     * Mark a task open
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function open($task_id)
    {
        $result = $this->db
                        ->table(self::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => 1,
                            'date_completed' => ''
                        ));

        if ($result) {
            $this->event->trigger(self::EVENT_OPEN, array('task_id' => $task_id) + $this->getById($task_id));
        }

        return $result;
    }

    /**
     * Remove a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function remove($task_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    /**
     * Move a task to another column or to another position
     *
     * @access public
     * @param  integer    $task_id     Task id
     * @param  integer    $column_id   Column id
     * @param  integer    $position    Position (must be greater than 1)
     * @return boolean
     */
    public function move($task_id, $column_id, $position)
    {
        return $this->update(array(
            'id' => $task_id,
            'column_id' => $column_id,
            'position' => $position,
        ));
    }

    /**
     * Validate task creation
     *
     * @access public
     * @param  array    $values           Form values
     * @return array    $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('color_id', t('The color is required')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('column_id', t('The column is required')),
            new Validators\Integer('column_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\Date('date_due', t('Invalid date'), t('m/d/Y')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate description creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateDescriptionCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('description', t('The description is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate task modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('color_id', t('The color is required')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('column_id', t('The column is required')),
            new Validators\Integer('column_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\Date('date_due', t('Invalid date'), t('m/d/Y')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate assignee change
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateAssigneeModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('owner_id', t('This value is required')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Parse a date (different format for each locale) to a timestamp
     *
     * @access public
     * @param  string   $value  Date to parse
     * @param  string   $format Date format
     * @return integer
     */
    public function getTimestampFromDate($value, $format)
    {
        $date = \DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = \DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? $timestamp : 0;
            }
        }

        return 0;
    }
}
