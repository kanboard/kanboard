<?php

namespace Model;

require_once __DIR__.'/base.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

class User extends Base
{
    const TABLE = 'users';

    public function getById($user_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $user_id)->findOne();
    }

    public function getByUsername($username)
    {
        return $this->db->table(self::TABLE)->eq('username', $username)->findOne();
    }

    public function getAll()
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('username')
                    ->columns('id', 'username', 'is_admin', 'default_project_id')
                    ->findAll();
    }

    public function getList()
    {
        return $this->db->table(self::TABLE)->asc('username')->listing('id', 'username');
    }

    public function create(array $values)
    {
        if (isset($values['confirmation'])) unset($values['confirmation']);
        $values['password'] = \password_hash($values['password'], PASSWORD_BCRYPT);

        return $this->db->table(self::TABLE)->save($values);
    }

    public function update(array $values)
    {
        if (! empty($values['password'])) {
            $values['password'] = \password_hash($values['password'], PASSWORD_BCRYPT);
        }
        else {
            unset($values['password']);
        }

        unset($values['confirmation']);

        $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);

        if ($_SESSION['user']['id'] == $values['id']) {
            $this->updateSession();
        }

        return true;
    }

    public function remove($user_id)
    {
        $this->db->startTransaction();

        // All tasks assigned to this user will be unassigned
        $this->db->table(Task::TABLE)->eq('owner_id', $user_id)->update(array('owner_id' => ''));
        $this->db->table(self::TABLE)->eq('id', $user_id)->remove();

        $this->db->closeTransaction();

        return true;
    }

    public function updateSession(array $user = array())
    {
        if (empty($user)) {
            $user = $this->getById($_SESSION['user']['id']);
        }

        if (isset($user['password'])) unset($user['password']);

        $_SESSION['user'] = $user;
    }

    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('username', t('The username is required')),
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\AlphaNumeric('username', t('The username must be alphanumeric')),
            new Validators\Unique('username', t('The username must be unique'), $this->db->getConnection(), self::TABLE, 'id'),
            new Validators\Required('password', t('The password is required')),
            new Validators\MinLength('password', t('The minimum length is %d characters', 6), 6),
            new Validators\Required('confirmation', t('The confirmation is required')),
            new Validators\Equals('password', 'confirmation', t('Passwords doesn\'t matches')),
            new Validators\Integer('default_project_id', t('This value must be an integer')),
            new Validators\Integer('is_admin', t('This value must be an integer')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateModification(array $values)
    {
        if (! empty($values['password'])) {
            return $this->validateCreation($values);
        }
        else {

            $v = new Validator($values, array(
                new Validators\Required('id', t('The user id is required')),
                new Validators\Required('username', t('The username is required')),
                new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
                new Validators\AlphaNumeric('username', t('The username must be alphanumeric')),
                new Validators\Unique('username', t('The username must be unique'), $this->db->getConnection(), self::TABLE, 'id'),
                new Validators\Integer('default_project_id', t('This value must be an integer')),
                new Validators\Integer('is_admin', t('This value must be an integer')),
            ));
        }

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    public function validateLogin(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('username', t('The username is required')),
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\Required('password', t('The password is required')),
        ));

        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result) {

            $user = $this->getByUsername($values['username']);

            if ($user !== false && \password_verify($values['password'], $user['password'])) {
                $this->updateSession($user);
            }
            else {
                $result = false;
                $errors['login'] = t('Bad username or password');
            }
        }

        return array(
            $result,
            $errors
        );
    }
}
