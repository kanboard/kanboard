<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Column Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ColumnModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'columns';

    /**
     * Get a column by the id
     *
     * @access public
     * @param  integer  $column_id    Column id
     * @return array
     */
    public function getById($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->findOne();
    }

    /**
     * Get projectId by the columnId
     *
     * @access public
     * @param  integer  $column_id    Column id
     * @return integer
     */
    public function getProjectId($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->findOneColumn('project_id');
    }

    /**
     * Get the first column id for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function getFirstColumnId($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findOneColumn('id');
    }

    /**
     * Get the last column id for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function getLastColumnId($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->desc('position')->findOneColumn('id');
    }

    /**
     * Get the position of the last column for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return integer
     */
    public function getLastColumnPosition($project_id)
    {
        return (int) $this->db
                        ->table(self::TABLE)
                        ->eq('project_id', $project_id)
                        ->desc('position')
                        ->findOneColumn('position');
    }

    /**
     * Get a column id by the name
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $title
     * @return integer
     */
    public function getColumnIdByTitle($project_id, $title)
    {
        return (int) $this->db->table(self::TABLE)->eq('project_id', $project_id)->eq('title', $title)->findOneColumn('id');
    }

    /**
     * Get a column title by the id
     *
     * @access public
     * @param  integer  $column_id
     * @return integer
     */
    public function getColumnTitleById($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->findOneColumn('title');
    }

    /**
     * Get all columns sorted by position for a given project
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->asc('position')->findAll();
    }

    /**
     * Get all columns with task count
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getAllWithTaskCount($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns('id', 'title', 'position', 'task_limit', 'description', 'hide_in_dashboard', 'project_id')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE column_id=".self::TABLE.".id AND is_active='1'", 'nb_open_tasks')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE column_id=".self::TABLE.".id AND is_active='0'", 'nb_closed_tasks')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    /**
     * Get all columns with task count
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @return array
     */
    public function getAllWithPerSwimlaneTaskCount($project_id, $swimlane_id)
    {
        return $this->db->table(self::TABLE)
            ->columns('id', 'title', 'position', 'task_limit', 'description', 'hide_in_dashboard', 'project_id', $swimlane_id.' AS swimlane_id')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE column_id=".self::TABLE.".id AND swimlane_id=".$swimlane_id." AND is_active='1'", 'nb_open_tasks')
            ->subquery("SELECT COUNT(*) FROM ".TaskModel::TABLE." WHERE column_id=".self::TABLE.".id AND swimlane_id=".$swimlane_id." AND is_active='0'", 'nb_closed_tasks')
            ->eq('project_id', $project_id)
            ->asc('position')
            ->findAll();
    }

    /**
     * Get the list of columns sorted by position [ column_id => title ]
     *
     * @access public
     * @param  integer  $project_id   Project id
     * @param  boolean  $prepend      Prepend a default value
     * @return array
     */
    public function getList($project_id, $prepend = false)
    {
        $listing = $this->db->hashtable(self::TABLE)->eq('project_id', $project_id)->asc('position')->getAll('id', 'title');
        return $prepend ? array(-1 => t('All columns')) + $listing : $listing;
    }

    /**
     * Add a new column to the board
     *
     * @access public
     * @param  integer $project_id  Project id
     * @param  string  $title       Column title
     * @param  integer $task_limit  Task limit
     * @param  string  $description Column description
     * @param  integer $hide_in_dashboard
     * @return bool|int
     */
    public function create($project_id, $title, $task_limit = 0, $description = '', $hide_in_dashboard = 0)
    {
        $values = array(
            'project_id' => $project_id,
            'title' => $title,
            'task_limit' => intval($task_limit),
            'position' => $this->getLastColumnPosition($project_id) + 1,
            'hide_in_dashboard' => $hide_in_dashboard,
            'description' => $description,
        );

        return $this->db->table(self::TABLE)->persist($values);
    }

    /**
     * Update a column
     *
     * @access public
     * @param  integer   $column_id     Column id
     * @param  string    $title         Column title
     * @param  integer   $task_limit    Task limit
     * @param  string    $description   Optional description
     * @param  integer   $hide_in_dashboard
     * @return boolean
     */
    public function update($column_id, $title, $task_limit = 0, $description = '', $hide_in_dashboard = 0)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->update(array(
            'title' => $title,
            'task_limit' => intval($task_limit),
            'hide_in_dashboard' => $hide_in_dashboard,
            'description' => $description,
        ));
    }

    /**
     * Remove a column and all tasks associated to this column
     *
     * @access public
     * @param  integer  $column_id    Column id
     * @return boolean
     */
    public function remove($column_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $column_id)->remove();
    }

    /**
     * Change column position
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $column_id
     * @param  integer  $position
     * @return boolean
     */
    public function changePosition($project_id, $column_id, $position)
    {
        if ($position < 1 || $position > $this->db->table(self::TABLE)->eq('project_id', $project_id)->count()) {
            return false;
        }

        $column_ids = $this->db->table(self::TABLE)->eq('project_id', $project_id)->neq('id', $column_id)->asc('position')->findAllByColumn('id');
        $offset = 1;
        $results = array();

        foreach ($column_ids as $current_column_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table(self::TABLE)->eq('id', $current_column_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(self::TABLE)->eq('id', $column_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }
}
