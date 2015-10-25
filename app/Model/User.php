<?php

namespace Kanboard\Model;

use PicoDb\Database;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Core\Session;
use Kanboard\Core\Security\Token;

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
     * Return true if the user exists
     *
     * @access public
     * @param  integer    $user_id   User id
     * @return boolean
     */
    public function exists($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->exists();
    }

    /**
     * Get query to fetch all users
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        'id',
                        'username',
                        'name',
                        'email',
                        'is_admin',
                        'is_project_admin',
                        'is_ldap_user',
                        'notifications_enabled',
                        'google_id',
                        'github_id',
                        'twofactor_activated'
                    );
    }

    /**
     * Return the full name
     *
     * @param  array    $user   User properties
     * @return string
     */
    public function getFullname(array $user)
    {
        return $user['name'] ?: $user['username'];
    }

    /**
     * Return true is the given user id is administrator
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return boolean
     */
    public function isAdmin($user_id)
    {
        return $this->userSession->isAdmin() ||  // Avoid SQL query if connected
               $this->db
                    ->table(User::TABLE)
                    ->eq('id', $user_id)
                    ->eq('is_admin', 1)
                    ->exists();
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
     * @return array|boolean
     */
    public function getByGoogleId($google_id)
    {
        if (empty($google_id)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('google_id', $google_id)->findOne();
    }

    /**
     * Get a specific user by the Github id
     *
     * @access public
     * @param  string  $github_id  Github user id
     * @return array|boolean
     */
    public function getByGithubId($github_id)
    {
        if (empty($github_id)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('github_id', $github_id)->findOne();
    }

    /**
     * Get a specific user by the Gitlab id
     *
     * @access public
     * @param  string  $gitlab_id  Gitlab user id
     * @return array|boolean
     */
    public function getByGitlabId($gitlab_id)
    {
        if (empty($gitlab_id)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('gitlab_id', $gitlab_id)->findOne();
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
     * Get user_id by username
     *
     * @access public
     * @param  string  $username  Username
     * @return array
     */
    public function getIdByUsername($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->findOneColumn('id');
    }

    /**
     * Get a specific user by the email address
     *
     * @access public
     * @param  string  $email  Email
     * @return array|boolean
     */
    public function getByEmail($email)
    {
        if (empty($email)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('email', $email)->findOne();
    }

    /**
     * Fetch user by using the token
     *
     * @access public
     * @param  string   $token    Token
     * @return array|boolean
     */
    public function getByToken($token)
    {
        if (empty($token)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('token', $token)->findOne();
    }

    /**
     * Get all users
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->getQuery()->asc('username')->findAll();
    }

    /**
     * Get the number of users
     *
     * @access public
     * @return integer
     */
    public function count()
    {
        return $this->db->table(self::TABLE)->count();
    }

    /**
     * List all users (key-value pairs with id/username)
     *
     * @access public
     * @param  boolean  $prepend  Prepend "All users"
     * @return array
     */
    public function getList($prepend = false)
    {
        $users = $this->db->table(self::TABLE)->columns('id', 'username', 'name')->findAll();
        $listing = $this->prepareList($users);

        if ($prepend) {
            return array(User::EVERYBODY_ID => t('Everybody')) + $listing;
        }

        return $listing;
    }

    /**
     * Common method to prepare a user list
     *
     * @access public
     * @param  array     $users    Users list (from database)
     * @return array               Formated list
     */
    public function prepareList(array $users)
    {
        $result = array();

        foreach ($users as $user) {
            $result[$user['id']] = $this->getFullname($user);
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
            } else {
                unset($values['password']);
            }
        }

        $this->removeFields($values, array('confirmation', 'current_password'));
        $this->resetFields($values, array('is_admin', 'is_ldap_user', 'is_project_admin', 'disable_login_form'));
        $this->convertNullFields($values, array('gitlab_id'));
        $this->convertIntegerFields($values, array('gitlab_id'));
    }

    /**
     * Add a new user in the database
     *
     * @access public
     * @param  array  $values  Form values
     * @return boolean|integer
     */
    public function create(array $values)
    {
        $this->prepare($values);
        return $this->persist(self::TABLE, $values);
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
        if (Session::isOpen() && $this->userSession->getId() == $values['id']) {
            $this->userSession->refresh();
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
        return $this->db->transaction(function (Database $db) use ($user_id) {

            // All assigned tasks are now unassigned (no foreign key)
            if (! $db->table(Task::TABLE)->eq('owner_id', $user_id)->update(array('owner_id' => 0))) {
                return false;
            }

            // All assigned subtasks are now unassigned (no foreign key)
            if (! $db->table(Subtask::TABLE)->eq('user_id', $user_id)->update(array('user_id' => 0))) {
                return false;
            }

            // All comments are not assigned anymore (no foreign key)
            if (! $db->table(Comment::TABLE)->eq('user_id', $user_id)->update(array('user_id' => 0))) {
                return false;
            }

            // All private projects are removed
            $project_ids = $db->table(Project::TABLE)
                           ->eq('is_private', 1)
                           ->eq(ProjectPermission::TABLE.'.user_id', $user_id)
                           ->join(ProjectPermission::TABLE, 'project_id', 'id')
                           ->findAllByColumn(Project::TABLE.'.id');

            if (! empty($project_ids)) {
                $db->table(Project::TABLE)->in('id', $project_ids)->remove();
            }

            // Finally remove the user
            if (! $db->table(User::TABLE)->eq('id', $user_id)->remove()) {
                return false;
            }
        });
    }

    /**
     * Enable public access for a user
     *
     * @access public
     * @param  integer   $user_id   User id
     * @return bool
     */
    public function enablePublicAccess($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $user_id)
                    ->save(array('token' => Token::getToken()));
    }

    /**
     * Disable public access for a user
     *
     * @access public
     * @param  integer   $user_id    User id
     * @return bool
     */
    public function disablePublicAccess($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $user_id)
                    ->save(array('token' => ''));
    }

    /**
     * Get the number of failed login for the user
     *
     * @access public
     * @param  string  $username
     * @return integer
     */
    public function getFailedLogin($username)
    {
        return (int) $this->db->table(self::TABLE)->eq('username', $username)->findOneColumn('nb_failed_login');
    }

    /**
     * Reset to 0 the counter of failed login
     *
     * @access public
     * @param  string  $username
     * @return boolean
     */
    public function resetFailedLogin($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->update(array('nb_failed_login' => 0, 'lock_expiration_date' => 0));
    }

    /**
     * Increment failed login counter
     *
     * @access public
     * @param  string  $username
     * @return boolean
     */
    public function incrementFailedLogin($username)
    {
        return $this->db->execute('UPDATE '.self::TABLE.' SET nb_failed_login=nb_failed_login+1 WHERE username=?', array($username)) !== false;
    }

    /**
     * Check if the account is locked
     *
     * @access public
     * @param  string  $username
     * @return boolean
     */
    public function isLocked($username)
    {
        return $this->db->table(self::TABLE)
            ->eq('username', $username)
            ->neq('lock_expiration_date', 0)
            ->gte('lock_expiration_date', time())
            ->exists();
    }

    /**
     * Lock the account for the specified duration
     *
     * @access public
     * @param  string   $username   Username
     * @param  integer  $duration   Duration in minutes
     * @return boolean
     */
    public function lock($username, $duration = 15)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->update(array('lock_expiration_date' => time() + $duration * 60));
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
            new Validators\Integer('is_admin', t('This value must be an integer')),
            new Validators\Integer('is_project_admin', t('This value must be an integer')),
            new Validators\Integer('is_ldap_user', t('This value must be an integer')),
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

        if (isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1) {
            $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        } else {
            $v = new Validator($values, array_merge($rules, $this->commonValidationRules(), $this->commonPasswordValidationRules()));
        }

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
            if ($this->authentication->authenticate($this->session['user']['username'], $values['current_password'])) {
                return array(true, array());
            } else {
                return array(false, array('current_password' => array(t('Wrong password'))));
            }
        }

        return array(false, $v->getErrors());
    }
}
