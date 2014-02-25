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
        return $this->db
                    ->table(self::TABLE)
                    ->columns('tasks.id', 'title', 'color_id', 'project_id', 'owner_id', 'column_id', 'position', 'score', 'users.username')
                    ->join('users', 'id', 'owner_id')
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->asc('position')
                    ->findAll();
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

        $values['date_creation'] = time();
        $values['position'] = $this->countByColumnId($values['project_id'], $values['column_id']);

        $this->db->table(self::TABLE)->save($values);

        $task_id = $this->db->getConnection()->getLastId();

        $this->db->closeTransaction();

        return $task_id;
    }

    public function update(array $values)
    {
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);
    }

    public function close($task_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $task_id)
            ->update(array(
                'is_active' => 0,
                'date_completed' => time()
            ));
    }

    public function open($task_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $task_id)
            ->update(array(
                'is_active' => 1,
                'date_completed' => ''
            ));
    }

    public function remove($task_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_id)->remove();
    }

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
