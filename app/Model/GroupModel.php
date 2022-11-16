<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Group Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class GroupModel extends Base
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
        return $this->db->table(self::TABLE)
            ->columns('id', 'name', 'external_id')
            ->subquery('SELECT COUNT(*) FROM '.GroupMemberModel::TABLE.' WHERE group_id='.self::TABLE.'.id', 'nb_users');
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
        return $this->db->table(self::TABLE)->eq('id', $group_id)->findOne();
    }

    /**
     * Get a specific group by externalID
     *
     * @access public
     * @param  string $external_id
     * @return array
     */
    public function getByExternalId($external_id)
    {
        return $this->db->table(self::TABLE)->eq('external_id', $external_id)->findOne();
    }

    /**
     * Get specific groups by externalIDs
     *
     * @access public
     * @param  string[] $external_ids
     * @return array
     */
    public function getByExternalIds(array $external_ids)
    {
        if (empty($external_ids)) {
            return [];
        }

        return $this->db->table(self::TABLE)->in('external_id', $external_ids)->findAll();
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
     * @return boolean
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
        return $this->db->table(self::TABLE)->persist(array(
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
        $updates = $values;
        unset($updates['id']);
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->update($updates);
    }

    /**
     * Get groupId from externalGroupId and create the group if not found
     *
     * @access public
     * @param  string $name
     * @param  string $external_id
     * @return bool|integer
     */
    public function getOrCreateExternalGroupId($name, $external_id)
    {
        $group_id = $this->db->table(self::TABLE)->eq('external_id', $external_id)->findOneColumn('id');

        if (empty($group_id)) {
            $group_id = $this->create($name, $external_id);
        }

        return $group_id;
    }
}
