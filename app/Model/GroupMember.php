<?php

namespace Kanboard\Model;

/**
 * Group Member Model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class GroupMember extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'group_has_users';

    /**
     * Get query to fetch all users
     *
     * @access public
     * @param  integer $group_id
     * @return \PicoDb\Table
     */
    public function getQuery($group_id)
    {
        return $this->db->table(self::TABLE)
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('group_id', $group_id);
    }

    /**
     * Get all users
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function getMembers($group_id)
    {
        return $this->getQuery($group_id)->findAll();
    }

    /**
     * Get all not members
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function getNotMembers($group_id)
    {
        $subquery = $this->db->table(self::TABLE)
            ->columns('user_id')
            ->eq('group_id', $group_id);

        return $this->db->table(User::TABLE)
            ->notInSubquery('id', $subquery)
            ->findAll();
    }

    /**
     * Add user to a group
     *
     * @access public
     * @param  integer $group_id
     * @param  integer $user_id
     * @return boolean
     */
    public function addUser($group_id, $user_id)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'group_id' => $group_id,
            'user_id' => $user_id,
        ));
    }

    /**
     * Remove user from a group
     *
     * @access public
     * @param  integer $group_id
     * @param  integer $user_id
     * @return boolean
     */
    public function removeUser($group_id, $user_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('group_id', $group_id)
            ->eq('user_id', $user_id)
            ->remove();
    }

    /**
     * Check if a user is member
     *
     * @access public
     * @param  integer $group_id
     * @param  integer $user_id
     * @return boolean
     */
    public function isMember($group_id, $user_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('group_id', $group_id)
            ->eq('user_id', $user_id)
            ->exists();
    }
}
