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
     * Get all tags associated to a task
     *
     * @access public
     * @param  integer $task_id
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->db->table(TagModel::TABLE)
            ->columns(TagModel::TABLE.'.id', TagModel::TABLE.'.name')
            ->eq(self::TABLE.'.task_id', $task_id)
            ->join(self::TABLE, 'tag_id', 'id')
            ->findAll();
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
        $tags = $this->getAll($task_id);
        return array_column($tags, 'name', 'id');
    }

    /**
     * Add or update a list of tags to a task
     *
     * @access public
     * @param integer  $project_id
     * @param integer  $task_id
     * @param string[] $tags
     * @return boolean
     */
    public function save($project_id, $task_id, array $tags)
    {
        $task_tags = $this->getList($task_id);

        return $this->addTags($project_id, $task_id, $task_tags, $tags) &&
            $this->removeTags($task_id, $task_tags, $tags);
    }

    /**
     * Associate a tag to a task
     *
     * @access public
     * @param  integer  $task_id
     * @param  integer  $tag_id
     * @return boolean
     */
    public function associate($task_id, $tag_id)
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
    public function dissociate($task_id, $tag_id)
    {
        return $this->db->table(self::TABLE)
            ->eq('task_id', $task_id)
            ->eq('tag_id', $tag_id)
            ->remove();
    }

    private function addTags($project_id, $task_id, $task_tags, $tags)
    {
        foreach ($tags as $tag) {
            $tag_id = $this->tagModel->findOrCreateTag($project_id, $tag);

            if (! isset($task_tags[$tag_id]) && ! $this->associate($task_id, $tag_id)) {
                return false;
            }
        }

        return true;
    }

    private function removeTags($task_id, $task_tags, $tags)
    {
        foreach ($task_tags as $tag_id => $tag) {
            if (! in_array($tag, $tags)) {
                if (! $this->dissociate($task_id, $tag_id)) {
                    return false;
                }
            }
        }

        return true;
    }
}
