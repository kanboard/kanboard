<?php

namespace Kanboard\Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Custom Filter model
 *
 * @package  model
 * @author   Timo Litzbarski
 */
class CustomFilter extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'custom_filters';

    /**
     * Return the list of all allowed custom filters for a user and project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $user_id       User id
     * @return array
     */
    public function getAll($project_id, $user_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                User::TABLE.'.name as owner_name',
                User::TABLE.'.username as owner_username',
                self::TABLE.'.id',
                self::TABLE.'.user_id',
                self::TABLE.'.project_id',
                self::TABLE.'.filter',
                self::TABLE.'.name',
                self::TABLE.'.is_shared',
                self::TABLE.'.append'
            )
            ->asc(self::TABLE.'.name')
            ->join(User::TABLE, 'id', 'user_id')
            ->beginOr()
            ->eq('is_shared', 1)
            ->eq('user_id', $user_id)
            ->closeOr()
            ->eq('project_id', $project_id)
            ->findAll();
    }

    /**
     * Get custom filter by id
     *
     * @access private
     * @param  integer   $filter_id
     * @return array
     */
    public function getById($filter_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $filter_id)->findOne();
    }

    /**
     * Create a custom filter
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool|integer
     */
    public function create(array $values)
    {
        return $this->persist(self::TABLE, $values);
    }

    /**
     * Update a custom filter
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $values['id'])
            ->update($values);
    }

    /**
     * Remove a custom filter
     *
     * @access public
     * @param  integer  $filter_id
     * @return bool
     */
    public function remove($filter_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $filter_id)->remove();
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
            new Validators\Required('project_id', t('Field required')),
            new Validators\Required('user_id', t('Field required')),
            new Validators\Required('name', t('Field required')),
            new Validators\Required('filter', t('Field required')),
            new Validators\Integer('user_id', t('This value must be an integer')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 100), 100),
            new Validators\MaxLength('filter', t('The maximum length is %d characters', 100), 100)
        );
    }

    /**
     * Validate filter creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, $this->commonValidationRules());

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate filter modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('Field required')),
            new Validators\Integer('id', t('This value must be an integer')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
