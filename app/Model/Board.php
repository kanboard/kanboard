<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Board model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Board extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'columns';

    /**
     * Save task positions for each column
     *
     * @access public
     * @param  array    $positions          [['task_id' => X, 'column_id' => X, 'position' => X], ...]
     * @param  integer  $selected_task_id   The selected task id
     * @return boolean
     */
    public function saveTasksPosition(array $positions, $selected_task_id)
    {
        $this->db->startTransaction();

        foreach ($positions as $value) {

            // We trigger events only for the selected task
            if (! $this->task->move($value['task_id'], $value['column_id'], $value['position'], $value['task_id'] == $selected_task_id)) {
                $this->db->cancelTransaction();
                return false;
            }
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Create a board with default columns, must be executed inside a transaction
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  array    $columns      List of columns title ['column1', 'column2', ...]
     * @return boolean
     */
    public function create($project_id, array $columns)
    {
        $position = 0;

        foreach ($columns as $title) {

            $values = array(
                'title' => $title,
                'position' => ++$position,
                'project_id' => $project_id,
            );

            if (! $this->db->table(self::TABLE)->save($values)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add a new column to the board
     *
     * @access public
     * @param  array    $values   ['title' => X, 'project_id' => X]
     * @return boolean
     */
    public function add(array $values)
    {
        $values['position'] = $this->getLastColumnPosition($values['project_id']) + 1;
        return $this->db->table(self::TABLE)->save($values);
    }

    /**
     * Update columns
     *
     * @access public
     * @param  array    $values   Form values
     * @return boolean
     */
    public function update(array $values)
    {
        $this->db->startTransaction();

        foreach (array('title', 'task_limit') as $field) {
            foreach ($values[$field] as $column_id => $field_value) {

                if ($field === 'task_limit' && empty($field_value)) {
                    $field_value = 0;
                }

                $this->updateColumn($column_id, array($field => $field_value));
            }
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Update a column
     *
     * @access public
     * @param  integer  $column_id  Column id
     * @param  array    $values     Form values
     * @return boolean
     */
    public function updateColumn($column_id, array $values)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->update($values);
    }

    /**
     * Move a column down, increment the column position value
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  integer  $column_id    Column id
     * @return boolean
     */
    public function moveDown($project_id, $column_id)
    {
        $columns = $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->listing('id', 'position');
        $positions = array_flip($columns);

        if (isset($columns[$column_id]) && $columns[$column_id] < count($columns)) {

            $position = ++$columns[$column_id];
            $columns[$positions[$position]]--;

            $this->db->startTransaction();
            $this->db->table(self::TABLE)->eq('id', $column_id)->update(array('position' => $position));
            $this->db->table(self::TABLE)->eq('id', $positions[$position])->update(array('position' => $columns[$positions[$position]]));
            $this->db->closeTransaction();

            return true;
        }

        return false;
    }

    /**
     * Move a column up, decrement the column position value
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  integer  $column_id    Column id
     * @return boolean
     */
    public function moveUp($project_id, $column_id)
    {
        $columns = $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->listing('id', 'position');
        $positions = array_flip($columns);

        if (isset($columns[$column_id]) && $columns[$column_id] > 1) {

            $position = --$columns[$column_id];
            $columns[$positions[$position]]++;

            $this->db->startTransaction();
            $this->db->table(self::TABLE)->eq('id', $column_id)->update(array('position' => $position));
            $this->db->table(self::TABLE)->eq('id', $positions[$position])->update(array('position' => $columns[$positions[$position]]));
            $this->db->closeTransaction();

            return true;
        }

        return false;
    }

    /**
     * Get all columns and tasks for a given project
     *
     * @access public
     * @param  integer $project_id Project id
     * @param array $filters
     * @return array
     */
    public function get($project_id, array $filters = array())
    {
        $this->db->startTransaction();

        $columns = $this->getColumns($project_id);

        $filters[] = array('column' => 'project_id', 'operator' => 'eq', 'value' => $project_id);
        $filters[] = array('column' => 'is_active', 'operator' => 'eq', 'value' => Task::STATUS_OPEN);

        $tasks = $this->task->find($filters);

        foreach ($columns as &$column) {

            $column['tasks'] = array();

            foreach ($tasks as &$task) {
                if ($task['column_id'] == $column['id']) {
                    $column['tasks'][] = $task;
                }
            }
        }

        $this->db->closeTransaction();

        return $columns;
    }

    /**
     * Get the first column id for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function getFirstColumn($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findOneColumn('id');
    }

    /**
     * Get the list of columns sorted by position [ column_id => title ]
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getColumnsList($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->listing('id', 'title');
    }

    /**
     * Get all columns sorted by position for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getColumns($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findAll();
    }

    /**
     * Get the number of columns for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function countColumns($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->count();
    }

    /**
     * Get a column by the id
     *
     * @access public
     * @param  integer  $column_id    Column id
     * @return array
     */
    public function getColumn($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->findOne();
    }

    /**
     * Get the position of the last column for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function getLastColumnPosition($project_id)
    {
        return (int) $this->db
                        ->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->desc('position')
                        ->findOneColumn('position');
    }

    /**
     * Remove a column and all tasks associated to this column
     *
     * @access public
     * @param  integer  $column_id    Column id
     * @return boolean
     */
    public function removeColumn($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->remove();
    }

    /**
     * Validate column modification
     *
     * @access public
     * @param  array   $columns          Original columns List
     * @param  array   $values           Required parameters to update a column
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $columns, array $values)
    {
        $rules = array();

        foreach ($columns as $column_id => $column_title) {
            $rules[] = new Validators\Integer('task_limit['.$column_id.']', t('This value must be an integer'));
            $rules[] = new Validators\GreaterThan('task_limit['.$column_id.']', t('This value must be greater than %d', 0), 0);
            $rules[] = new Validators\Required('title['.$column_id.']', t('The title is required'));
            $rules[] = new Validators\MaxLength('title['.$column_id.']', t('The maximum length is %d characters', 50), 50);
        }

        $v = new Validator($values, $rules);

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate column creation
     *
     * @access public
     * @param  array   $values           Required parameters to save an action
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('title', t('The title is required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 50), 50),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
