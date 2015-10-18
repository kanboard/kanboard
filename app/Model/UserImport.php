<?php

namespace Kanboard\Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Core\Csv;

/**
 * User Import
 *
 * @package  model
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
            'is_project_admin' => 'Project Administrator',
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
            if ($this->user->create($row)) {
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

        foreach (array('is_admin', 'is_project_admin', 'is_ldap_user') as $field) {
            $row[$field] = Csv::getBooleanValue($row[$field]);
        }

        $this->removeEmptyFields($row, array('password', 'email', 'name'));

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
            new Validators\Unique('username', t('The username must be unique'), $this->db->getConnection(), User::TABLE, 'id'),
            new Validators\MinLength('password', t('The minimum length is %d characters', 6), 6),
            new Validators\Email('email', t('Email address invalid')),
            new Validators\Integer('is_admin', t('This value must be an integer')),
            new Validators\Integer('is_project_admin', t('This value must be an integer')),
            new Validators\Integer('is_ldap_user', t('This value must be an integer')),
        ));

        return $v->execute();
    }
}
