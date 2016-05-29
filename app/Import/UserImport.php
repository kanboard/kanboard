<?php

namespace Kanboard\Import;

use Kanboard\Model\UserModel;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Base;
use Kanboard\Core\Csv;

/**
 * User Import
 *
 * @package  import
 * @author   Frederic Guillot
 */
class UserImport extends Base
{
    /**
     * Number of successful import
     *
     * @access public
     * @var integer
     */
    public $counter = 0;

    /**
     * Get mapping between CSV header and SQL columns
     *
     * @access public
     * @return array
     */
    public function getColumnMapping()
    {
        return array(
            'username'         => 'Username',
            'password'         => 'Password',
            'email'            => 'Email',
            'name'             => 'Full Name',
            'is_admin'         => 'Administrator',
            'is_manager'       => 'Manager',
            'is_ldap_user'     => 'Remote User',
        );
    }

    /**
     * Import a single row
     *
     * @access public
     * @param  array   $row
     * @param  integer $line_number
     */
    public function import(array $row, $line_number)
    {
        $row = $this->prepare($row);

        if ($this->validateCreation($row)) {
            if ($this->userModel->create($row) !== false) {
                $this->logger->debug('UserImport: imported successfully line '.$line_number);
                $this->counter++;
            } else {
                $this->logger->error('UserImport: creation error at line '.$line_number);
            }
        } else {
            $this->logger->error('UserImport: validation error at line '.$line_number);
        }
    }

    /**
     * Format row before validation
     *
     * @access public
     * @param  array   $row
     * @return array
     */
    public function prepare(array $row)
    {
        $row['username'] = strtolower($row['username']);

        foreach (array('is_admin', 'is_manager', 'is_ldap_user') as $field) {
            $row[$field] = Csv::getBooleanValue($row[$field]);
        }

        if ($row['is_admin'] == 1) {
            $row['role'] = Role::APP_ADMIN;
        } elseif ($row['is_manager'] == 1) {
            $row['role'] = Role::APP_MANAGER;
        } else {
            $row['role'] = Role::APP_USER;
        }

        unset($row['is_admin']);
        unset($row['is_manager']);

        $this->helper->model->removeEmptyFields($row, array('password', 'email', 'name'));

        return $row;
    }

    /**
     * Validate user creation
     *
     * @access public
     * @param  array   $values
     * @return boolean
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('username', t('The username must be unique'), $this->db->getConnection(), UserModel::TABLE, 'id'),
            new Validators\MinLength('password', t('The minimum length is %d characters', 6), 6),
            new Validators\Email('email', t('Email address invalid')),
            new Validators\Integer('is_ldap_user', t('This value must be an integer')),
        ));

        return $v->execute();
    }
}
