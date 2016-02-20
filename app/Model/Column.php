<?php

namespace Kanboard\Model;

/**
 * Column Model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Column extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'columns';

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
