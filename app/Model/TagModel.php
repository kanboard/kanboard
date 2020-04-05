<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class TagModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class TagModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'tags';

    /**
     * Get all tags
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->asc('name')->findAll();
    }

    /**
     * Get all tags by project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getAllByProject($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('name')->findAll();
    }

    /**
     * Get assignable tags for a project
     *
     * @param  integer $project_id          Project Id
     * @param  bool    $include_global_tags Flag to include global tags
     * @return array
     */
    public function getAssignableList($project_id, $include_global_tags = true)
    {
        if ($include_global_tags) {
            return $this->db->hashtable(self::TABLE)
                ->beginOr()
                ->eq('project_id', $project_id)
                ->eq('project_id', 0)
                ->closeOr()
                ->asc('name')
                ->getAll('id', 'name');
        } else {
            return $this->db->hashtable(self::TABLE)
                ->beginOr()
                ->eq('project_id', $project_id)
                ->closeOr()
                ->asc('name')
                ->getAll('id', 'name');
        }
    }

    /**
     * Get one tag
     *
     * @access public
     * @param  integer $tag_id
     * @return array|null
     */
    public function getById($tag_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $tag_id)->findOne();
    }

    /**
     * Get tag id from tag name
     *
     * @access public
     * @param  int    $project_id
     * @param  string $tag
     * @return integer
     */
    public function getIdByName($project_id, $tag)
    {
        return $this->db
            ->table(self::TABLE)
            ->beginOr()
            ->eq('project_id', 0)
            ->eq('project_id', $project_id)
            ->closeOr()
            ->ilike('name', $tag)
            ->asc('project_id')
            ->findOneColumn('id');
    }

    /**
     * Return true if the tag exists
     *
     * @access public
     * @param  integer $project_id
     * @param  string  $tag
     * @param  integer $tag_id
     * @return boolean
     */
    public function exists($project_id, $tag, $tag_id = 0)
    {
        return $this->db
            ->table(self::TABLE)
            ->neq('id', $tag_id)
            ->beginOr()
            ->eq('project_id', 0)
            ->eq('project_id', $project_id)
            ->closeOr()
            ->ilike('name', $tag)
            ->asc('project_id')
            ->exists();
    }

    /**
     * Return tag id and create a new tag if necessary
     *
     * @access public
     * @param  int    $project_id
     * @param  string $tag
     * @return bool|int
     */
    public function findOrCreateTag($project_id, $tag)
    {
        $tag_id = $this->getIdByName($project_id, $tag);

        if (empty($tag_id)) {
            $tag_id = $this->create($project_id, $tag);
        }

        return $tag_id;
    }

    /**
     * Add a new tag
     *
     * @access public
     * @param  int    $project_id
     * @param  string $tag
     * @return bool|int
     */
    public function create($project_id, $tag, $color_id = null)
    {
        return $this->db->table(self::TABLE)->persist(array(
            'project_id' => $project_id,
            'name' => $tag,
            'color_id' => $color_id,
        ));
    }

    /**
     * Update a tag
     *
     * @access public
     * @param  integer $tag_id
     * @param  string  $tag
     * @return bool
     */
    public function update($tag_id, $tag, $color_id = null, $project_id = null)
    {
        if ($project_id !== null) {
            return $this->db->table(self::TABLE)->eq('id', $tag_id)->update(array(
                'name' => $tag,
                'color_id' => $color_id,
                'project_id' => $project_id,
            ));
        } else {
            return $this->db->table(self::TABLE)->eq('id', $tag_id)->update(array(
                'name' => $tag,
                'color_id' => $color_id,
            ));
        }
    }

    /**
     * Remove a tag
     *
     * @access public
     * @param  integer $tag_id
     * @return bool
     */
    public function remove($tag_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $tag_id)->remove();
    }
}
