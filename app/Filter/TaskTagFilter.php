<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\TagModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskTagModel;
use PicoDb\Database;

/**
 * Class TaskTagFilter
 *
 * @package Kanboard\Filter
 * @author  Frederic Guillot
 */
class TaskTagFilter extends BaseFilter implements FilterInterface
{
    /**
     * Database object
     *
     * @access private
     * @var Database
     */
    private $db;

    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('tag');
    }

    /**
     * Set database object
     *
     * @access public
     * @param  Database $db
     * @return $this
     */
    public function setDatabase(Database $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        if ($this->value === 'none') {
            $task_ids = $this->getTaskIdsWithoutTags();
        } else {
            $task_ids = $this->getTaskIdsWithGivenTag();
        }

        if (empty($task_ids)) {
            $task_ids = array(-1);
        }

        $this->query->in(TaskModel::TABLE.'.id', $task_ids);

        return $this;
    }

    protected function getTaskIdsWithoutTags()
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->asc(TaskModel::TABLE . '.project_id')
            ->left(TaskTagModel::TABLE, 'tg', 'task_id', TaskModel::TABLE, 'id')
            ->isNull('tg.tag_id')
            ->findAllByColumn(TaskModel::TABLE . '.id');
    }

    protected function getTaskIdsWithGivenTag()
    {
        return $this->db
            ->table(TagModel::TABLE)
            ->ilike(TagModel::TABLE.'.name', $this->value)
            ->asc(TagModel::TABLE.'.project_id')
            ->join(TaskTagModel::TABLE, 'tag_id', 'id')
            ->findAllByColumn(TaskTagModel::TABLE.'.task_id');
    }
}
