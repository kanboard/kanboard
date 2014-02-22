<?php

namespace Model;

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Project extends Base
{
    const TABLE = 'projects';
    const ACTIVE = 1;
    const INACTIVE = 0;

    public function get($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->findOne();
    }

    public function getFirst()
    {
        return $this->db->table(self::TABLE)->findOne();
    }

    public function getAll($fetch_stats = false)
    {
        if (! $fetch_stats) {
            return $this->db->table(self::TABLE)->asc('name')->findAll();
        }

        $this->db->startTransaction();

        $projects = $this->db
                        ->table(self::TABLE)
                        ->asc('name')
                        ->findAll();

        $taskModel = new \Model\Task;
        $boardModel = new \Model\Board;

        foreach ($projects as &$project) {

            $columns = $boardModel->getcolumns($project['id']);
            $project['nb_active_tasks'] = 0;

            foreach ($columns as &$column) {
                $column['nb_active_tasks'] = $taskModel->countByColumnId($project['id'], $column['id']);
                $project['nb_active_tasks'] += $column['nb_active_tasks'];
            }

            $project['columns'] = $columns;
            $project['nb_tasks'] = $taskModel->countByProjectId($project['id']);
            $project['nb_inactive_tasks'] = $project['nb_tasks'] - $project['nb_active_tasks'];
        }

        $this->db->closeTransaction();

        return $projects;
    }

    public function getList()
    {
        return array(t('None')) + $this->db->table(self::TABLE)->asc('name')->listing('id', 'name');
    }

    public function getAllByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('name')
                    ->eq('is_active', $status)
                    ->findAll();
    }

    public function getListByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('name')
                    ->eq('is_active', $status)
                    ->listing('id', 'name');
    }

    public function countByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('is_active', $status)
                    ->count();
    }

    public function create(array $values)
    {
        $this->db->startTransaction();

        $this->db->table(self::TABLE)->save($values);

        $project_id = $this->db->getConnection()->getLastId();

        $boardModel = new \Model\Board;

        $boardModel->create($project_id, array(
            t('Backlog'),
            t('Ready'),
            t('Work in progress'),
            t('Done'),
        ));

        $this->db->closeTransaction();

        return $project_id;
    }

    public function update(array $values)
    {
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
    }

    public function remove($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->remove();
    }

    public function enable($project_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_active' => 1));
    }

    public function disable($project_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_active' => 0));
    }

    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('name', t('The project name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('name', t('This project must be unique'), $this->db->getConnection(), self::TABLE)
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateModification(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('id', t('The project id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('name', t('The project name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('name', t('This project must be unique'), $this->db->getConnection(), self::TABLE)
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
