<?php

namespace Kanboard\Api;

use LogicException;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Ldap\Client as LdapClient;
use Kanboard\Core\Ldap\ClientException as LdapException;
use Kanboard\Core\Ldap\User as LdapUser;

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

    public function createUser($username, $password, $name = '', $email = '', $role = Role::APP_USER)
    {
        $values = array(
            'username' => $username,
            'password' => $password,
            'confirmation' => $password,
            'name' => $name,
            'email' => $email,
            'role' => $role,
        );

        list($valid, ) = $this->userValidator->validateCreation($values);
        return $valid ? $this->user->create($values) : false;
    }

    public function createLdapUser($username)
    {
        try {

            $ldap = LdapClient::connect();
            $user = LdapUser::getUser($ldap, sprintf(LDAP_USER_FILTER, $username));

            if ($user === null) {
                $this->logger->info('User not found in LDAP server');
                return false;
            }

            if ($user->getUsername() === '') {
                throw new LogicException('Username not found in LDAP profile, check the parameter LDAP_USER_ATTRIBUTE_USERNAME');
            }

            $values = array(
                'username' => $user->getUsername(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'is_ldap_user' => 1,
            );

            return $this->user->create($values);

        } catch (LdapException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $username = null, $name = null, $email = null, $role = null)
    {
        $values = array(
            'id' => $id,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'role' => $role,
        );

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        list($valid, ) = $this->userValidator->validateApiModification($values);
        return $valid && $this->user->update($values);
    }
}
