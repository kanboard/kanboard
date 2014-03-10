<?php

namespace Model;

require_once __DIR__.'/base.php';
require_once __DIR__.'/task.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Board extends Base
{
    const TABLE = 'columns';

    // Save the board (each task position/column)
    public function saveTasksPosition(array $values)
    {
        $this->db->startTransaction();

        $results = array();
        $taskModel = new Task($this->db, $this->event);

        foreach ($values as $value) {
            $results[] = $taskModel->move(
                $value['task_id'],
                $value['column_id'],
                $value['position']
            );
        }

        $this->db->closeTransaction();

        return ! in_array(false, $results, true);
    }

    // Create board with default columns => must be executed inside a transaction
    public function create($project_id, array $columns)
    {
        $position = 0;

        foreach ($columns as $title) {

            $values = array(
                'title' => $title,
                'position' => ++$position,
                'project_id' => $project_id,
            );

            $this->db->table(self::TABLE)->save($values);
        }

        return true;
    }

    // Add a new column to the board
    public function add(array $values)
    {
        $values['position'] = $this->getLastColumnPosition($values['project_id']) + 1;
        return $this->db->table(self::TABLE)->save($values);
    }

    // Update columns
    public function update(array $values)
    {
        $this->db->startTransaction();

        foreach (array('title', 'task_limit') as $field) {
            foreach ($values[$field] as $column_id => $field_value) {
                $this->db->table(self::TABLE)->eq('id', $column_id)->update(array($field => $field_value));
            }
        }

        $this->db->closeTransaction();

        return true;
    }

    // Get columns and tasks for each column
    public function get($project_id)
    {
        $this->db->startTransaction();

        $columns = $this->getColumns($project_id);
        $taskModel = new Task($this->db, $this->event);

        foreach ($columns as &$column) {
            $column['tasks'] = $taskModel->getAllByColumnId($project_id, $column['id'], array(1));
        }

        $this->db->closeTransaction();

        return $columns;
    }

    // Get first column id for a given project
    public function getFirstColumn($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findOneColumn('id');
    }

    // Get list of columns
    public function getColumnsList($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->listing('id', 'title');
    }

    // Get all columns information for a project
    public function getColumns($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findAll();
    }

    // Get the number of columns for a project
    public function countColumns($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->count();
    }

    // Get just one column
    public function getColumn($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->findOne();
    }

    // Get the position of the last column for a project
    public function getLastColumnPosition($project_id)
    {
        return (int) $this->db
                        ->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->desc('position')
                        ->findOneColumn('position');
    }

    // Remove a column and all tasks associated to this column
    public function removeColumn($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->remove();
    }

    // Validate columns update
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

    // Validate column creation
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