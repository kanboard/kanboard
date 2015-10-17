<?php

namespace Kanboard\Api;

use Kanboard\Auth\Ldap;

/**
 * User API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class User extends \Kanboard\Core\Base
{
    public function getUser($user_id)
    {
        return $this->user->getById($user_id);
    }

    public function getAllUsers()
    {
        return $this->user->getAll();
    }

    public function removeUser($user_id)
    {
        return $this->user->remove($user_id);
    }

    public function createUser($username, $password, $name = '', $email = '', $is_admin = 0, $is_project_admin = 0)
    {
        $values = array(
            'username' => $username,
            'password' => $password,
            'confirmation' => $password,
            'name' => $name,
            'email' => $email,
            'is_admin' => $is_admin,
            'is_project_admin' => $is_project_admin,
        );

        list($valid, ) = $this->user->validateCreation($values);
        return $valid ? $this->user->create($values) : false;
    }

    public function createLdapUser($username = '', $email = '', $is_admin = 0, $is_project_admin = 0)
    {
        $ldap = new Ldap($this->container);
        $user = $ldap->lookup($username, $email);

        if (! $user) {
            return false;
        }

        $values = array(
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'is_ldap_user' => 1,
            'is_admin' => $is_admin,
            'is_project_admin' => $is_project_admin,
        );

        return $this->user->create($values);
    }

    public function updateUser($id, $username = null, $name = null, $email = null, $is_admin = null, $is_project_admin = null)
    {
        $values = array(
            'id' => $id,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'is_admin' => $is_admin,
            'is_project_admin' => $is_project_admin,
        );

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        list($valid, ) = $this->user->validateApiModification($values);
        return $valid && $this->user->update($values);
    }
}
