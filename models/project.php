<?php

namespace Model;

require_once __DIR__.'/base.php';
require_once __DIR__.'/acl.php';
require_once __DIR__.'/board.php';
require_once __DIR__.'/task.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class Project extends Base
{
    const TABLE = 'projects';
    const TABLE_USERS = 'project_has_users';
    const ACTIVE = 1;
    const INACTIVE = 0;

    // Get a list of people that can by assigned for tasks
    public function getUsersList($project_id, $prepend = true)
    {
        $allowed_users = $this->getAllowedUsers($project_id);
        $userModel = new User($this->db, $this->event);

        if (empty($allowed_users)) {
            $allowed_users = $userModel->getList();
        }

        if ($prepend) {
            return array(t('Unassigned')) + $allowed_users;
        }

        return $allowed_users;
    }

    // Get a list of allowed people for a project
    public function getAllowedUsers($project_id)
    {
        return $this->db
            ->table(self::TABLE_USERS)
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->asc('username')
            ->listing('user_id', 'username');
    }

    // Get allowed and not allowed users for a project
    public function getAllUsers($project_id)
    {
        $users = array(
            'allowed' => array(),
            'not_allowed' => array(),
        );

        $userModel = new User($this->db, $this->event);
        $all_users = $userModel->getList();

        $users['allowed'] = $this->getAllowedUsers($project_id);

        foreach ($all_users as $user_id => $username) {

            if (! isset($users['allowed'][$user_id])) {
                $users['not_allowed'][$user_id] = $username;
            }
        }

        return $users;
    }

    // Allow a specific user for a given project
    public function allowUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE_USERS)
                    ->save(array('project_id' => $project_id, 'user_id' => $user_id));
    }

    // Revoke a specific user for a given project
    public function revokeUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->remove();
    }

    // Check if a specific user is allowed to access to a given project
    public function isUserAllowed($project_id, $user_id)
    {
        // If there is nobody specified, everybody have access to the project
        $nb_users = $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->count();

        if ($nb_users < 1) return true;

        // Check if user has admin rights
        $nb_users = $this->db
                    ->table(User::TABLE)
                    ->eq('id', $user_id)
                    ->eq('is_admin', 1)
                    ->count();

        if ($nb_users > 0) return true;

        // Otherwise, allow only specific users
        return (bool) $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->count();
    }

    public function getById($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->findOne();
    }

    public function getByToken($token)
    {
        return $this->db->table(self::TABLE)->eq('token', $token)->findOne();
    }

    public function getFirst()
    {
        return $this->db->table(self::TABLE)->findOne();
    }

    public function getAll($fetch_stats = false, $check_permissions = false)
    {
        if (! $fetch_stats) {
            return $this->db->table(self::TABLE)->asc('name')->findAll();
        }

        $this->db->startTransaction();

        $projects = $this->db
                        ->table(self::TABLE)
                        ->asc('name')
                        ->findAll();

        $boardModel = new Board($this->db, $this->event);
        $taskModel = new Task($this->db, $this->event);
        $aclModel = new Acl($this->db, $this->event);

        foreach ($projects as $pkey => &$project) {

            if ($check_permissions && ! $this->isUserAllowed($project['id'], $aclModel->getUserId())) {
                unset($projects[$pkey]);
            }
            else {

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
        }

        $this->db->closeTransaction();

        return $projects;
    }

    public function getList($prepend = true)
    {
        if ($prepend) {
            return array(t('None')) + $this->db->table(self::TABLE)->asc('name')->listing('id', 'name');
        }

        return $this->db->table(self::TABLE)->asc('name')->listing('id', 'name');
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

    public function filterListByAccess(array $projects, $user_id)
    {
        foreach ($projects as $project_id => $project_name) {
            if (! $this->isUserAllowed($project_id, $user_id)) {
                unset($projects[$project_id]);
            }
        }

        return $projects;
    }

    public function create(array $values)
    {
        $this->db->startTransaction();

        $values['token'] = self::generateToken();

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $project_id = $this->db->getConnection()->getLastId();

        $boardModel = new Board($this->db, $this->event);
        $boardModel->create($project_id, array(
            t('Backlog'),
            t('Ready'),
            t('Work in progress'),
            t('Done'),
        ));

        $this->db->closeTransaction();

        return (int) $project_id;
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

    public function validateUserAccess(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('user_id', t('The user id is required')),
            new Validators\Integer('user_id', t('This value must be an integer')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
