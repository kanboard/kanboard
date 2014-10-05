<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Core\Session;

/**
 * User model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class User extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'users';

    /**
     * Id used for everbody (filtering)
     *
     * @var integer
     */
    const EVERYBODY_ID = -1;

    /**
     * Return true is the given user id is administrator
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function isAdmin($user_id)
    {
        $result = $this->db
                    ->table(User::TABLE)
                    ->eq('id', $user_id)
                    ->eq('is_admin', 1)
                    ->count();

        return $result > 0;
    }

    /**
     * Get the default project from the session
     *
     * @access public
     * @return integer
     */
    public function getFavoriteProjectId()
    {
        return isset($_SESSION['user']['default_project_id']) ? $_SESSION['user']['default_project_id'] : 0;
    }

    /**
     * Get the last seen project from the session
     *
     * @access public
     * @return integer
     */
    public function getLastSeenProjectId()
    {
        return empty($_SESSION['user']['last_show_project_id']) ? 0 : $_SESSION['user']['last_show_project_id'];
    }

    /**
     * Set the last seen project from the session
     *
     * @access public
     * @@param integer    $project_id    Project id
     */
    public function storeLastSeenProjectId($project_id)
    {
        $_SESSION['user']['last_show_project_id'] = (int) $project_id;
    }

    /**
     * Get a specific user by id
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getById($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->findOne();
    }

    /**
     * Get a specific user by the Google id
     *
     * @access public
     * @param  string  $google_id  Google unique id
     * @return array
     */
    public function getByGoogleId($google_id)
    {
        return $this->db->table(self::TABLE)->eq('google_id', $google_id)->findOne();
    }

    /**
     * Get a specific user by the GitHub id
     *
     * @access public
     * @param  string  $github_id  GitHub user id
     * @return array
     */
    public function getByGitHubId($github_id)
    {
        return $this->db->table(self::TABLE)->eq('github_id', $github_id)->findOne();
    }

    /**
     * Get a specific user by the username
     *
     * @access public
     * @param  string  $username  Username
     * @return array
     */
    public function getByUsername($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->findOne();
    }

    /**
     * Get all users
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('username')
                    ->columns('id', 'username', 'name', 'email', 'is_admin', 'default_project_id', 'is_ldap_user', 'notifications_enabled', 'google_id', 'github_id')
                    ->findAll();
    }

    /**
     * List all users (key-value pairs with id/username)
     *
     * @access public
     * @return array
     */
    public function getList()
    {
        $users = $this->db->table(self::TABLE)->columns('id', 'username', 'name')->findAll();

        $result = array();

        foreach ($users as $user) {
            $result[$user['id']] = $user['name'] ?: $user['username'];
        }

        asort($result);

        return $result;
    }

    /**
     * Prepare values before an update or a create
     *
     * @access public
     * @param  array    $values    Form values
     */
    public function prepare(array &$values)
    {
        if (isset($values['password'])) {

            if (! empty($values['password'])) {
                $values['password'] = \password_hash($values['password'], PASSWORD_BCRYPT);
            }
            else {
                unset($values['password']);
            }
        }

        $this->removeFields($values, array('confirmation', 'current_password'));
        $this->resetFields($values, array('is_admin', 'is_ldap_user'));
    }

    /**
     * Add a new user in the database
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean
     */
    public function create(array $values)
    {
        $this->prepare($values);
        return $this->db->table(self::TABLE)->save($values);
    }

    /**
     * Modify a new user
     *
     * @access public
     * @param  array  $values  Form values
     * @return array
     */
    public function update(array $values)
    {
        $this->prepare($values);
        $result = $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);

        // If the user is connected refresh his session
        if (Session::isOpen() && $_SESSION['user']['id'] == $values['id']) {
            $this->updateSession();
        }

        return $result;
    }

    /**
     * Remove a specific user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return boolean
     */
    public function remove($user_id)
    {
        $this->db->startTransaction();

        // All tasks assigned to this user will be unassigned
        $this->db->table(Task::TABLE)->eq('owner_id', $user_id)->update(array('owner_id' => 0));
        $result = $this->db->table(self::TABLE)->eq('id', $user_id)->remove();

        $this->db->closeTransaction();

        return $result;
    }

    /**
     * Update user session information
     *
     * @access public
     * @param  array  $user  User data
     */
    public function updateSession(array $user = array())
    {
        if (empty($user)) {
            $user = $this->getById($_SESSION['user']['id']);
        }

        if (isset($user['password'])) {
            unset($user['password']);
        }

        $user['id'] = (int) $user['id'];
        $user['default_project_id'] = (int) $user['default_project_id'];
        $user['is_admin'] = (bool) $user['is_admin'];
        $user['is_ldap_user'] = (bool) $user['is_ldap_user'];

        $_SESSION['user'] = $user;
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('username', t('The username must be unique'), $this->db->getConnection(), self::TABLE, 'id'),
            new Validators\Email('email', t('Email address invalid')),
            new Validators\Integer('default_project_id', t('This value must be an integer')),
            new Validators\Integer('is_admin', t('This value must be an integer')),
        );
    }

    /**
     * Common password validation rules
     *
     * @access private
     * @return array
     */
    private function commonPasswordValidationRules()
    {
        return array(
            new Validators\Required('password', t('The password is required')),
            new Validators\MinLength('password', t('The minimum length is %d characters', 6), 6),
            new Validators\Required('confirmation', t('The confirmation is required')),
            new Validators\Equals('password', 'confirmation', t('Passwords don\'t match')),
        );
    }

    /**
     * Validate user creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('username', t('The username is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules(), $this->commonPasswordValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate user modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The user id is required')),
            new Validators\Required('username', t('The username is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate user API modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateApiModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The user id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate password modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validatePasswordModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The user id is required')),
            new Validators\Required('current_password', t('The current password is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonPasswordValidationRules()));

        if ($v->execute()) {

            // Check password
            if ($this->authentication->authenticate($_SESSION['user']['username'], $values['current_password'])) {
                return array(true, array());
            }
            else {
                return array(false, array('current_password' => array(t('Wrong password'))));
            }
        }

        return array(false, $v->getErrors());
    }
}
