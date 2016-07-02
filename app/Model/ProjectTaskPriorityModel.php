<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Project Task Priority Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectTaskPriorityModel extends Base
{
    /**
     * Get Priority range from a project
     *
     * @access public
     * @param  array $project
     * @return array
     */
    public function getPriorities(array $project)
    {
        $range = range($project['priority_start'], $project['priority_end']);
        return array_combine($range, $range);
    }

    /**
     * Get task priority settings
     *
     * @access public
     * @param  int $project_id
     * @return array|null
     */
    public function getPrioritySettings($project_id)
    {
        return $this->db
            ->table(ProjectModel::TABLE)
            ->columns('priority_default', 'priority_start', 'priority_end')
            ->eq('id', $project_id)
            ->findOne();
    }

    /**
     * Get default task priority
     *
     * @access public
     * @param  int $project_id
     * @return int
     */
    public function getDefaultPriority($project_id)
    {
        return $this->db->table(ProjectModel::TABLE)->eq('id', $project_id)->findOneColumn('priority_default') ?: 0;
    }

    /**
     * Get priority for a destination project
     *
     * @access public
     * @param  integer $dst_project_id
     * @param  integer $priority
     * @return integer
     */
    public function getPriorityForProject($dst_project_id, $priority)
    {
        $settings = $this->getPrioritySettings($dst_project_id);

        if ($priority >= $settings['priority_start'] && $priority <= $settings['priority_end']) {
            return $priority;
        }

        return $settings['priority_default'];
    }
}
