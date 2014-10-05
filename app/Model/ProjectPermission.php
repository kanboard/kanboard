<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Project permission model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectPermission extends Base
{
    /**
     * SQL table name for permissions
     *
     * @var string
     */
    const TABLE = 'project_has_users';

    /**
     * Get a list of people that can be assigned for tasks
     *
     * @access public
     * @param  integer   $project_id            Project id
     * @param  bool      $prepend_unassigned    Prepend the 'Unassigned' value
     * @param  bool      $prepend_everybody     Prepend the 'Everbody' value
     * @return array
     */
    public function getUsersList($project_id, $prepend_unassigned = true, $prepend_everybody = false)
    {
        $allowed_users = $this->getAllowedUsers($project_id);

        if ($prepend_unassigned) {
            $allowed_users = array(t('Unassigned')) + $allowed_users;
        }

        if ($prepend_everybody) {
            $allowed_users = array(User::EVERYBODY_ID => t('Everybody')) + $allowed_users;
        }

        return $allowed_users;
    }

    /**
     * Get a list of allowed people for a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getAllowedUsers($project_id)
    {
        $users = $this->db
            ->table(self::TABLE)
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->asc('username')
            ->columns(User::TABLE.'.id', User::TABLE.'.username', User::TABLE.'.name')
            ->findAll();

        $result = array();

        foreach ($users as $user) {
            $result[$user['id']] = $user['name'] ?: $user['username'];
        }

        asort($result);

        return $result;
    }

    /**
     * Get allowed and not allowed users for a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getAllUsers($project_id)
    {
        $users = array(
            'allowed' => array(),
            'not_allowed' => array(),
        );

        $all_users = $this->user->getList();

        $users['allowed'] = $this->getAllowedUsers($project_id);

        foreach ($all_users as $user_id => $username) {

            if (! isset($users['allowed'][$user_id])) {
                $users['not_allowed'][$user_id] = $username;
            }
        }

        return $users;
    }

    /**
     * Allow a specific user for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function allowUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->save(array('project_id' => $project_id, 'user_id' => $user_id));
    }

    /**
     * Revoke a specific user for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function revokeUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->remove();
    }

    /**
     * Check if a specific user is allowed to access to a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function isUserAllowed($project_id, $user_id)
    {
        if ($this->user->isAdmin($user_id)) {
            return true;
        }

        return (bool) $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->count();
    }

    /**
     * Check if a specific user is allowed to manage a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function adminAllowed($project_id, $user_id)
    {
        if ($this->isUserAllowed($project_id, $user_id) && $this->project->isPrivate($project_id)) {
            return true;
        }

        return false;
    }

    /**
     * Filter a list of projects for a given user
     *
     * @access public
     * @param  array     $projects     Project list: ['project_id' => 'project_name']
     * @param  integer   $user_id      User id
     * @return array
     */
    public function filterProjects(array $projects, $user_id)
    {
        foreach ($projects as $project_id => $project_name) {
            if (! $this->isUserAllowed($project_id, $user_id)) {
                unset($projects[$project_id]);
            }
        }

        return $projects;
    }

    /**
     * Return a list of projects for a given user
     *
     * @access public
     * @param  integer   $user_id      User id
     * @return array
     */
    public function getAllowedProjects($user_id)
    {
        return $this->filterProjects($this->project->getListByStatus(Project::ACTIVE), $user_id);
    }

    /**
     * Copy user access from a project to another one
     *
     * @author Antonio Rabelo
     * @param  integer    $project_from      Project Template
     * @return integer    $project_to        Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_from, $project_to)
    {
        $users = $this->getAllowedUsers($project_from);

        foreach ($users as $user_id => $name) {
            if (! $this->allowUser($project_to, $user_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate allowed users
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
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
