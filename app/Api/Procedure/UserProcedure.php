<?php

namespace Kanboard\Api\Procedure;

use LogicException;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Ldap\Client as LdapClient;
use Kanboard\Core\Ldap\ClientException as LdapException;
use Kanboard\Core\Ldap\User as LdapUser;

/**
 * User API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class UserProcedure extends BaseProcedure
{
    public function getUser($user_id)
    {
        return $this->userModel->getById($user_id);
    }

    public function getUserByName($username)
    {
        return $this->userModel->getByUsername($username);
    }

    public function getAllUsers()
    {
        return $this->userModel->getAll();
    }

    public function removeUser($user_id)
    {
        return $this->userModel->remove($user_id);
    }

    public function disableUser($user_id)
    {
        return $this->userModel->disable($user_id);
    }

    public function enableUser($user_id)
    {
        return $this->userModel->enable($user_id);
    }

    public function isActiveUser($user_id)
    {
        return $this->userModel->isActive($user_id);
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
        return $valid ? $this->userModel->create($values) : false;
    }

    /**
     * Create LDAP user in the database
     *
     * Only "anonymous" and "proxy" LDAP authentication are supported by this method
     *
     * User information will be fetched from the LDAP server
     *
     * @access public
     * @param  string $username
     * @return bool|int
     */
    public function createLdapUser($username)
    {
        if (LDAP_BIND_TYPE === 'user') {
            $this->logger->error('LDAP authentication "user" is not supported by this API call');
            return false;
        }

        try {

            $ldap = LdapClient::connect();
            $ldap->setLogger($this->logger);
            $user = LdapUser::getUser($ldap, $username);

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
                'role' => $user->getRole() ?: Role::APP_USER,
                'is_ldap_user' => 1,
            );

            return $this->userModel->create($values);

        } catch (LdapException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $username = null, $name = null, $email = null, $role = null)
    {
        $values = $this->filterValues(array(
            'id' => $id,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ));

        list($valid, ) = $this->userValidator->validateApiModification($values);
        return $valid && $this->userModel->update($values);
    }
}
