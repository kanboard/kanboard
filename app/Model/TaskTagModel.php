<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class TaskTagModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class TaskTagModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_tags';

    /**
     * Get all tags not available in a project
     *
     * @access public
     * @param  integer $task_id
     * @param  integer $project_id
     * @return array
     */
    public function getTagIdsByTaskNotAvailableInProject($task_id, $project_id)
    {
        return $this->db->table(TagModel::TABLE)
            ->eq(self::TABLE.'.task_id', $task_id)
            ->notIn(TagModel::TABLE.'.project_id', array(0, $project_id))
            ->join(self::TABLE, 'tag_id', 'id')
            ->findAllByColumn(TagModel::TABLE.'.id');
    }

    /**
     * Get all tags associated to a task
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getTagsByTask($task_id)
    {
        return $this->db->table(TagModel::TABLE)
            ->columns(TagModel::TABLE.'.id', TagModel::TABLE.'.name', TagModel::TABLE.'.color_id')
            ->eq(self::TABLE.'.task_id', $task_id)
            ->join(self::TABLE, 'tag_id', 'id')
            ->findAll();
    }

    /**
     * Get all tags associated to a list of tasks
     *
     * @access public
     * @param  integer[] $task_ids
     * @return array
     */
    public function getTagsByTaskIds($task_ids)
    {
        if (empty($task_ids)) {
            return array();
        }

        $tags = $this->db->table(TagModel::TABLE)
            ->columns(TagModel::TABLE.'.id', TagModel::TABLE.'.name', TagModel::TABLE.'.color_id', self::TABLE.'.task_id')
            ->in(self::TABLE.'.task_id', $task_ids)
            ->join(self::TABLE, 'tag_id', 'id')
            ->asc(TagModel::TABLE.'.name')
            ->findAll();

        return array_column_index($tags, 'task_id');
    }

    /**
     * Get dictionary of tags
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getList($task_id)
    {
        $tags = $this->getTagsByTask($task_id);
        return array_column($tags, 'name', 'id');
    }

    /**
     * Add or update a list of tags to a task
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $task_id
     * @param  string[] $tags
     * @return boolean
     */
    public function save($project_id, $task_id, array $tags)
    {
        $task_tags = $this->getList($task_id);
        $tags = array_filter($tags);

        return $this->associateTags($project_id, $task_id, $task_tags, $tags) &&
            $this->dissociateTags($task_id, $task_tags, $tags);
    }

    /**
     * Associate a tag to a task
     *
     * @access public
     * @param  integer  $task_id
     * @param  integer  $tag_id
     * @return boolean
     */
    public function associateTag($task_id, $tag_id)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'task_id' => $task_id,
            'tag_id' => $tag_id,
        ));
    }

    /**
     * Dissociate a tag from a task
     *
     * @access public
     * @param  integer  $task_id
     * @param  integer  $tag_id
     * @return boolean
     */
    public function dissociateTag($task_id, $tag_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('task_id', $task_id)
            ->eq('tag_id', $tag_id)
            ->remove();
    }

    /**
     * Associate missing tags
     *
     * @access protected
     * @param  integer  $project_id
     * @param  integer  $task_id
     * @param  array    $task_tags
     * @param  string[] $tags
     * @return bool
     */
    protected function associateTags($project_id, $task_id, $task_tags, $tags)
    {
        foreach ($tags as $tag) {
            $tag_id = $this->tagModel->findOrCreateTag($project_id, $tag);

            if (! isset($task_tags[$tag_id]) && ! $this->associateTag($task_id, $tag_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Dissociate removed tags
     *
     * @access protected
     * @param  integer  $task_id
     * @param  array    $task_tags
     * @param  string[] $tags
     * @return bool
     */
    protected function dissociateTags($task_id, $task_tags, $tags)
    {
        foreach ($task_tags as $tag_id => $tag) {
            if (! in_array($tag, $tags)) {
                if (! $this->dissociateTag($task_id, $tag_id)) {
                    return false;
                }
            }
        }

        return true;
    }
}
