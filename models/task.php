<?php

namespace Model;

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Task extends Base
{
    const TABLE = 'tasks';

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
                        \Model\Project::TABLE.'.name AS project_name',
                        \Model\Board::TABLE.'.title AS column_title',
                        \Model\User::TABLE.'.username'
                    )
                    ->join(\Model\Project::TABLE, 'id', 'project_id')
                    ->join(\Model\Board::TABLE, 'id', 'column_id')
                    ->join(\Model\User::TABLE, 'id', 'owner_id')
                    ->eq(self::TABLE.'.id', $task_id)
                    ->findOne();
        }
        else {

            return $this->db->table(self::TABLE)->eq('id', $task_id)->findOne();
        }
    }

    public function getAllByProjectId($project_id, array $status = array(1, 0))
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
                \Model\Board::TABLE.'.title AS column_title',
                \Model\User::TABLE.'.username'
            )
            ->join(\Model\Board::TABLE, 'id', 'column_id')
            ->join(\Model\User::TABLE, 'id', 'owner_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->in('is_active', $status)
            ->desc('date_completed')
            ->findAll();
    }

    public function countByProjectId($project_id, $status = array(1, 0))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
                    ->count();
    }

    public function getAllByColumnId($project_id, $column_id, $status = array(1))
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

        $commentModel = new Comment;

        foreach ($tasks as &$task) {
            $task['nb_comments'] = $commentModel->count($task['id']);
        }

        return $tasks;
    }

    public function countByColumnId($project_id, $column_id, $status = array(1))
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->count();
    }

    public function create(array $values)
    {
        $this->db->startTransaction();

        unset($values['another_task']);

        if (! empty($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        $values['date_creation'] = time();
        $values['position'] = $this->countByColumnId($values['project_id'], $values['column_id']);

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        return $task_id;
    }

    public function update(array $values)
    {
        if (! empty($values['date_due'])) {
            $values['date_due'] = $this->getTimestampFromDate($values['date_due'], t('m/d/Y')) ?: null;
        }

        return $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);
    }

    // Mark a task closed
    public function close($task_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $task_id)
            ->update(array(
                'is_active' => 0,
                'date_completed' => time()
            ));
    }

    // Mark a task open
    public function open($task_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $task_id)
            ->update(array(
                'is_active' => 1,
                'date_completed' => ''
            ));
    }

    // Remove a task
    public function remove($task_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

    // Move a task to another column or to another position
    public function move($task_id, $column_id, $position)
    {
        return (bool) $this->db
                    ->table(self::TABLE)
                    ->eq('id', $task_id)
                    ->update(array('column_id' => $column_id, 'position' => $position));
    }

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
}
