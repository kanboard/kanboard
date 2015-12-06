<?php

namespace Kanboard\Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Group Model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Group extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'groups';

    /**
     * Get query to fetch all groups
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->db->table(self::TABLE);
    }

    /**
     * Get a specific group by id
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function getById($group_id)
    {
        return $this->getQuery()->eq('id', $group_id)->findOne();
    }

    /**
     * Get a specific group by external id
     *
     * @access public
     * @param  integer $external_id
     * @return array
     */
    public function getByExternalId($external_id)
    {
        return $this->getQuery()->eq('external_id', $external_id)->findOne();
    }

    /**
     * Get all groups
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->getQuery()->asc('name')->findAll();
    }

    /**
     * Search groups by name
     *
     * @access public
     * @param  string  $input
     * @return array
     */
    public function search($input)
    {
        return $this->db->table(self::TABLE)->ilike('name', '%'.$input.'%')->asc('name')->findAll();
    }

    /**
     * Remove a group
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function remove($group_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $group_id)->remove();
    }

    /**
     * Create a new group
     *
     * @access public
     * @param  string  $name
     * @param  string  $external_id
     * @return integer|boolean
     */
    public function create($name, $external_id = '')
    {
        return $this->persist(self::TABLE, array(
            'name' => $name,
            'external_id' => $external_id,
        ));
    }

    /**
     * Update existing group
     *
     * @access public
     * @param  array $values
     * @return boolean
     */
    public function update(array $values)
    {
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->update($values);
    }

    /**
     * Validate creation
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
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
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
            new Validators\Required('name', t('The name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 100), 100),
            new Validators\Unique('name', t('The name must be unique'), $this->db->getConnection(), self::TABLE, 'id'),
            new Validators\MaxLength('external_id', t('The maximum length is %d characters', 255), 255),
            new Validators\Integer('id', t('This value must be an integer')),
        );
    }
}
