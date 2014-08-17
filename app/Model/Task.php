<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use DateTime;
use PDO;

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
    const EVENT_CREATE_UPDATE = 'task.create_update';

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
     * Get a list of due tasks for all projects
     *
     * @access public
     * @return array
     */
    public function getTasksDue()
    {
        $tasks = $this->db->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.title',
                        self::TABLE.'.date_due',
                        self::TABLE.'.project_id',
                        Project::TABLE.'.name AS project_name',
                        User::TABLE.'.username AS assignee_username',
                        User::TABLE.'.name AS assignee_name'
                    )
                    ->join(Project::TABLE, 'id', 'project_id')
                    ->join(User::TABLE, 'id', 'owner_id')
                    ->eq(Project::TABLE.'.is_active', 1)
                    ->eq(self::TABLE.'.is_active', 1)
                    ->neq(self::TABLE.'.date_due', '')
                    ->lte(self::TABLE.'.date_due', mktime(23, 59, 59))
                    ->findAll();

        return $tasks;
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

            $sql = '
                SELECT
                tasks.id,
                tasks.title,
                tasks.description,
                tasks.date_creation,
                tasks.date_completed,
                tasks.date_modification,
                tasks.date_due,
                tasks.color_id,
                tasks.project_id,
                tasks.column_id,
                tasks.owner_id,
                tasks.creator_id,
                tasks.position,
                tasks.is_active,
                tasks.score,
                tasks.category_id,
                project_has_categories.name AS category_name,
                projects.name AS project_name,
                columns.title AS column_title,
                users.username AS assignee_username,
                users.name AS assignee_name,
                creators.username AS creator_username,
                creators.name AS creator_name
                FROM tasks
                LEFT JOIN users ON users.id = tasks.owner_id
                LEFT JOIN users AS creators ON creators.id = tasks.creator_id
                LEFT JOIN project_has_categories ON project_has_categories.id = tasks.category_id
                LEFT JOIN projects ON projects.id = tasks.project_id
                LEFT JOIN columns ON columns.id = tasks.column_id
                WHERE tasks.id = ?
            ';

            $rq = $this->db->execute($sql, array($task_id));
            return $rq->fetch(PDO::FETCH_ASSOC);
        }
        else {

            return $this->db->table(self::TABLE)->eq('id', $task_id)->findOne();
        }
    }

    /**
     * Count all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return array
     */
    public function getAll($project_id, array $status = array(self::STATUS_OPEN, self::STATUS_CLOSED))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
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
     * Get tasks that match defined filters
     *
     * @access public
     * @param  array    $filters   Filters: [ ['column' => '...', 'operator' => '...', 'value' => '...'], ... ]
     * @param  array    $sorting   Sorting: [ 'column' => 'date_creation', 'direction' => 'asc']
     * @return array
     */
    public function find(array $filters, array $sorting = array())
    {
        $table = $this->db
                    ->table(self::TABLE)
                    ->columns(
                        '(SELECT count(*) FROM comments WHERE task_id=tasks.id) AS nb_comments',
                        '(SELECT count(*) FROM task_has_files WHERE task_id=tasks.id) AS nb_files',
                        'tasks.id',
                        'tasks.title',
                        'tasks.description',
                        'tasks.date_creation',
                        'tasks.date_modification',
                        'tasks.date_completed',
                        'tasks.date_due',
                        'tasks.color_id',
                        'tasks.project_id',
                        'tasks.column_id',
                        'tasks.owner_id',
                        'tasks.creator_id',
                        'tasks.position',
                        'tasks.is_active',
                        'tasks.score',
                        'tasks.category_id',
                        'users.username AS assignee_username',
                        'users.name AS assignee_name'
                    )
                    ->join(User::TABLE, 'id', 'owner_id');

        foreach ($filters as $key => $filter) {

            if ($key === 'or') {

                $table->beginOr();

                foreach ($filter as $subfilter) {
                    $table->$subfilter['operator']($subfilter['column'], $subfilter['value']);
                }

                $table->closeOr();
            }
            else if (isset($filter['operator']) && isset($filter['column']) && isset($filter['value'])) {
                $table->$filter['operator']($filter['column'], $filter['value']);
            }
        }

        if (empty($sorting)) {
            $table->orderBy('tasks.position', 'ASC');
        }
        else {
            $table->orderBy($sorting['column'], $sorting['direction']);
        }

        return $table->findAll();
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
        $this->event->trigger(self::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $task);
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

        // Get the original task
        $task = $this->getById($task_id);

        // Cleanup data
        unset($task['id']);
        unset($task['date_completed']);

        // Assign new values
        $task['date_creation'] = time();
        $task['owner_id'] = 0;
        $task['category_id'] = 0;
        $task['is_active'] = 1;
        $task['column_id'] = $this->board->getFirstColumn($project_id);
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
        $this->event->trigger(self::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $task);
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $task);

        return $task_id;
    }

    /**
     * Prepare data before task creation or modification
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        if (isset($values['another_task'])) {
            unset($values['another_task']);
        }

        if (! empty($values['date_due']) && ! is_numeric($values['date_due'])) {
            $values['date_due'] = $this->parseDate($values['date_due']);
        }

        // Force integer fields at 0 (for Postgresql)
        if (isset($values['date_due']) && empty($values['date_due'])) {
            $values['date_due'] = 0;
        }

        if (isset($values['score']) && empty($values['score'])) {
            $values['score'] = 0;
        }
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
        $this->prepare($values);
        $values['date_creation'] = time();
        $values['date_modification'] = $values['date_creation'];
        $values['position'] = $this->countByColumnId($values['project_id'], $values['column_id']);

        // Save task
        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        // Trigger events
        $this->event->trigger(self::EVENT_CREATE_UPDATE, array('task_id' => $task_id) + $values);
        $this->event->trigger(self::EVENT_CREATE, array('task_id' => $task_id) + $values);

        return $task_id;
    }

    /**
     * Update a task
     *
     * @access public
     * @param  array    $values            Form values
     * @param  boolean  $trigger_events    Flag to trigger events
     * @return boolean
     */
    public function update(array $values, $trigger_events = true)
    {
        // Fetch original task
        $original_task = $this->getById($values['id']);

        if ($original_task === false) {
            return false;
        }

        // Prepare data
        $this->prepare($values);
        $updated_task = $values;
        unset($updated_task['id']);

        // We update the modification date only for the selected task to highlight recent moves
        if ($trigger_events) {
            $updated_task['date_modification'] = time();
        }

        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($updated_task);

        // Trigger events
        if ($result && $trigger_events) {
            $this->triggerUpdateEvents($original_task, $updated_task);
        }

        return $result;
    }

    /**
     * Trigger events for task modification
     *
     * @access public
     * @param  array    $original_task    Original task data
     * @param  array    $updated_task     Updated task data
     */
    public function triggerUpdateEvents(array $original_task, array $updated_task)
    {
        $events = array(
            self::EVENT_CREATE_UPDATE,
            self::EVENT_UPDATE,
        );

        if (isset($updated_task['column_id']) && $original_task['column_id'] != $updated_task['column_id']) {
            $events[] = self::EVENT_MOVE_COLUMN;
        }
        else if (isset($updated_task['position']) && $original_task['position'] != $updated_task['position']) {
            $events[] = self::EVENT_MOVE_POSITION;
        }

        $event_data = array_merge($original_task, $updated_task);
        $event_data['task_id'] = $original_task['id'];

        foreach ($events as $event) {
            $this->event->trigger($event, $event_data);
        }
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
        $this->file->removeAll($task_id);

        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    /**
     * Move a task to another column or to another position
     *
     * @access public
     * @param  integer    $task_id           Task id
     * @param  integer    $column_id         Column id
     * @param  integer    $position          Position (must be greater than 1)
     * @param  boolean    $trigger_events    Flag to trigger events
     * @return boolean
     */
    public function move($task_id, $column_id, $position, $trigger_events = true)
    {
        $this->event->clearTriggeredEvents();

        $values = array(
            'id' => $task_id,
            'column_id' => $column_id,
            'position' => $position,
        );

        return $this->update($values, $trigger_events);
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
            new Validators\Integer('creator_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 200), 200),
            new Validators\Date('date_due', t('Invalid date'), $this->getDateFormats()),
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
            new Validators\Date('date_due', t('Invalid date'), $this->getDateFormats()),
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
     * Return a timestamp if the given date format is correct otherwise return 0
     *
     * @access public
     * @param  string   $value  Date to parse
     * @param  string   $format Date format
     * @return integer
     */
    public function getValidDate($value, $format)
    {
        $date = DateTime::createFromFormat($format, $value);

        if ($date !== false) {
            $errors = DateTime::getLastErrors();
            if ($errors['error_count'] === 0 && $errors['warning_count'] === 0) {
                $timestamp = $date->getTimestamp();
                return $timestamp > 0 ? $timestamp : 0;
            }
        }

        return 0;
    }

    /**
     * Parse a date ad return a unix timestamp, try different date formats
     *
     * @access public
     * @param  string   $value   Date to parse
     * @return integer
     */
    public function parseDate($value)
    {
        foreach ($this->getDateFormats() as $format) {

            $timestamp = $this->getValidDate($value, $format);

            if ($timestamp !== 0) {
                return $timestamp;
            }
        }

        return null;
    }

    /**
     * Return the list of supported date formats
     *
     * @access public
     * @return array
     */
    public function getDateFormats()
    {
        return array(
            t('m/d/Y'),
            'Y-m-d',
            'Y_m_d',
        );
    }

    /**
     * For a given timestamp, reset the date to midnight
     *
     * @access public
     * @param  integer    $timestamp    Timestamp
     * @return integer
     */
    public function resetDateToMidnight($timestamp)
    {
        return mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
    }

    /**
     * Export a list of tasks for a given project and date range
     *
     * @access public
     * @param  integer    $project_id      Project id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to)
    {
        $sql = '
            SELECT
            tasks.id,
            projects.name AS project_name,
            tasks.is_active,
            project_has_categories.name AS category_name,
            columns.title AS column_title,
            tasks.position,
            tasks.color_id,
            tasks.date_due,
            creators.username AS creator_username,
            users.username AS assignee_username,
            tasks.score,
            tasks.title,
            tasks.date_creation,
            tasks.date_modification,
            tasks.date_completed
            FROM tasks
            LEFT JOIN users ON users.id = tasks.owner_id
            LEFT JOIN users AS creators ON creators.id = tasks.creator_id
            LEFT JOIN project_has_categories ON project_has_categories.id = tasks.category_id
            LEFT JOIN columns ON columns.id = tasks.column_id
            LEFT JOIN projects ON projects.id = tasks.project_id
            WHERE tasks.date_creation >= ? AND tasks.date_creation <= ? AND tasks.project_id = ?
        ';

        if (! is_numeric($from)) {
            $from = $this->resetDateToMidnight($this->parseDate($from));
        }

        if (! is_numeric($to)) {
            $to = $this->resetDateToMidnight(strtotime('+1 day', $this->parseDate($to)));
        }

        $rq = $this->db->execute($sql, array($from, $to, $project_id));
        $tasks = $rq->fetchAll(PDO::FETCH_ASSOC);

        $columns = array(
            t('Task Id'),
            t('Project'),
            t('Status'),
            t('Category'),
            t('Column'),
            t('Position'),
            t('Color'),
            t('Due date'),
            t('Creator'),
            t('Assignee'),
            t('Complexity'),
            t('Title'),
            t('Creation date'),
            t('Modification date'),
            t('Completion date'),
        );

        $results = array($columns);

        foreach ($tasks as &$task) {
            $results[] = array_values($this->formatOutput($task));
        }

        return $results;
    }

    /**
     * Format the output of a task array
     *
     * @access public
     * @param  array     $task    Task properties
     * @return array
     */
    public function formatOutput(array &$task)
    {
        $colors = $this->getColors();
        $task['score'] = $task['score'] ?: '';
        $task['is_active'] = $task['is_active'] == self::STATUS_OPEN ? t('Open') : t('Closed');
        $task['color_id'] = $colors[$task['color_id']];
        $task['date_creation'] = date('Y-m-d', $task['date_creation']);
        $task['date_due'] = $task['date_due'] ? date('Y-m-d', $task['date_due']) : '';
        $task['date_modification'] = $task['date_modification'] ? date('Y-m-d', $task['date_modification']) : '';
        $task['date_completed'] = $task['date_completed'] ? date('Y-m-d', $task['date_completed']) : '';

        return $task;
    }
}
