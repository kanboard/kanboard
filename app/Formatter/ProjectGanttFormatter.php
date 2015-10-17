<?php

namespace Kanboard\Formatter;

use Kanboard\Model\Project;

/**
 * Gantt chart formatter for projects
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class ProjectGanttFormatter extends Project implements FormatterInterface
{
    /**
     * List of projects
     *
     * @access private
     * @var array
     */
    private $projects = array();

    /**
     * Filter projects to generate the Gantt chart
     *
     * @access public
     * @param  int[]   $project_ids
     * @return ProjectGanttFormatter
     */
    public function filter(array $project_ids)
    {
        if (empty($project_ids)) {
            $this->projects = array();
        } else {
            $this->projects = $this->db
                ->table(self::TABLE)
                ->asc('start_date')
                ->in('id', $project_ids)
                ->eq('is_active', self::ACTIVE)
                ->eq('is_private', 0)
                ->findAll();
        }

        return $this;
    }

    /**
     * Format projects to be displayed in the Gantt chart
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $colors = $this->color->getDefaultColors();
        $bars = array();

        foreach ($this->projects as $project) {
            $start = empty($project['start_date']) ? time() : strtotime($project['start_date']);
            $end = empty($project['end_date']) ? $start : strtotime($project['end_date']);
            $color = next($colors) ?: reset($colors);

            $bars[] = array(
                'type' => 'project',
                'id' => $project['id'],
                'title' => $project['name'],
                'start' => array(
                    (int) date('Y', $start),
                    (int) date('n', $start),
                    (int) date('j', $start),
                ),
                'end' => array(
                    (int) date('Y', $end),
                    (int) date('n', $end),
                    (int) date('j', $end),
                ),
                'link' => $this->helper->url->href('project', 'show', array('project_id' => $project['id'])),
                'board_link' => $this->helper->url->href('board', 'show', array('project_id' => $project['id'])),
                'gantt_link' => $this->helper->url->href('gantt', 'project', array('project_id' => $project['id'])),
                'color' => $color,
                'not_defined' => empty($project['start_date']) || empty($project['end_date']),
                'users' => $this->projectPermission->getProjectUsers($project['id']),
            );
        }

        return $bars;
    }
}
