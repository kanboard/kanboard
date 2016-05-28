<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;

/**
 * Gantt chart formatter for projects
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class ProjectGanttFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Format projects to be displayed in the Gantt chart
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $projects = $this->query->findAll();
        $colors = $this->colorModel->getDefaultColors();
        $bars = array();

        foreach ($projects as $project) {
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
                'link' => $this->helper->url->href('ProjectViewController', 'show', array('project_id' => $project['id'])),
                'board_link' => $this->helper->url->href('BoardViewController', 'show', array('project_id' => $project['id'])),
                'gantt_link' => $this->helper->url->href('TaskGanttController', 'show', array('project_id' => $project['id'])),
                'color' => $color,
                'not_defined' => empty($project['start_date']) || empty($project['end_date']),
                'users' => $this->projectUserRoleModel->getAllUsersGroupedByRole($project['id']),
            );
        }

        return $bars;
    }
}
