<?php

namespace Kanboard\Model;

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
}
