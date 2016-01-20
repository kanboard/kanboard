<?php

namespace Kanboard\Model;

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
}
