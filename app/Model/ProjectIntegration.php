<?php

namespace Model;

/**
 * Project integration
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectIntegration extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_integrations';

    /**
     * Get all parameters for a project
     *
     * @access public
     * @param  integer  $project_id
     * @return array
     */
    public function getParameters($project_id)
    {
        return $this->db->table(self::TABLE)->eq('project_id', $project_id)->findOne() ?: array();
    }

    /**
     * Save parameters for a project
     *
     * @access public
     * @param  integer  $project_id
     * @param  array    $values
     * @return boolean
     */
    public function saveParameters($project_id, array $values)
    {
        if ($this->db->table(self::TABLE)->eq('project_id', $project_id)->count() === 1) {
            return $this->db->table(self::TABLE)->eq('project_id', $project_id)->update($values);
        }

        return $this->db->table(self::TABLE)->insert($values + array('project_id' => $project_id));
    }

    /**
     * Check if a project has the given parameter/value
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $option
     * @param  string   $value
     * @return boolean
     */
    public function hasValue($project_id, $option, $value)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq($option, $value)
                    ->count() === 1;
    }
}
