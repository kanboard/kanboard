<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Custom Filter model
 *
 * @package  Kanboard\Model
 * @author   Timo Litzbarski
 */
class CustomFilterModel extends Base
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
                UserModel::TABLE.'.name as owner_name',
                UserModel::TABLE.'.username as owner_username',
                self::TABLE.'.id',
                self::TABLE.'.user_id',
                self::TABLE.'.project_id',
                self::TABLE.'.filter',
                self::TABLE.'.name',
                self::TABLE.'.is_shared',
                self::TABLE.'.append'
            )
            ->asc(self::TABLE.'.name')
            ->join(UserModel::TABLE, 'id', 'user_id')
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
        return $this->db->table(self::TABLE)->persist($values);
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
        $updates = $values;
        unset($updates['id']);
        return $this->db->table(self::TABLE)
            ->eq('id', $values['id'])
            ->update($updates);
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
     * Duplicate custom filters from a project to another one, must be executed inside a transaction
     *
     * @param  integer    $src_project_id        Source project id
     * @param  integer    $dst_project_id        Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $filters = $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.user_id',
                self::TABLE.'.filter',
                self::TABLE.'.name',
                self::TABLE.'.is_shared',
                self::TABLE.'.append'
            )
            ->eq('project_id', $src_project_id)
            ->findAll();

        foreach ($filters as $filter) {
            $filter['project_id'] = $dst_project_id;
            // Avoid SQL error with Postgres
            $filter['is_shared'] = $filter['is_shared'] ?: 0;
            $filter['append'] = $filter['append'] ?: 0;

            if (! $this->db->table(self::TABLE)->save($filter)) {
                return false;
            }
        }

        return true;
    }
}
