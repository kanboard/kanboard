<?php

namespace Kanboard\Controller;

/**
 * Project Analytic controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Analytic extends Base
{
    /**
     * Common layout for analytic views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());
        $params['content_for_sublayout'] = $this->template->render($template, $params);

        return $this->template->layout('analytic/layout', $params);
    }

    /**
     * Show average Lead and Cycle time
     *
     * @access public
     */
    public function leadAndCycleTime()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->projectDailyStats->updateTotals($project['id'], date('Y-m-d'));

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $values['from'];
            $to = $values['to'];
        }

        $this->response->html($this->layout('analytic/lead_cycle_time', array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'project' => $project,
            'average' => $this->projectAnalytic->getAverageLeadAndCycleTime($project['id']),
            'metrics' => $this->projectDailyStats->getRawMetrics($project['id'], $from, $to),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t('Lead and Cycle time for "%s"', $project['name']),
        )));
    }

    /**
     * Show average time spent by column
     *
     * @access public
     */
    public function averageTimeByColumn()
    {
        $project = $this->getProject();

        $this->response->html($this->layout('analytic/avg_time_columns', array(
            'project' => $project,
            'metrics' => $this->projectAnalytic->getAverageTimeSpentByColumn($project['id']),
            'title' => t('Average time spent into each column for "%s"', $project['name']),
        )));
    }

    /**
     * Show tasks distribution graph
     *
     * @access public
     */
    public function tasks()
    {
        $project = $this->getProject();

        $this->response->html($this->layout('analytic/tasks', array(
            'project' => $project,
            'metrics' => $this->projectAnalytic->getTaskRepartition($project['id']),
            'title' => t('Task repartition for "%s"', $project['name']),
        )));
    }

    /**
     * Show users repartition
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProject();

        $this->response->html($this->layout('analytic/users', array(
            'project' => $project,
            'metrics' => $this->projectAnalytic->getUserRepartition($project['id']),
            'title' => t('User repartition for "%s"', $project['name']),
        )));
    }

    /**
     * Show cumulative flow diagram
     *
     * @access public
     */
    public function cfd()
    {
        $this->commonAggregateMetrics('analytic/cfd', 'total', 'Cumulative flow diagram for "%s"');
    }

    /**
     * Show burndown chart
     *
     * @access public
     */
    public function burndown()
    {
        $this->commonAggregateMetrics('analytic/burndown', 'score', 'Burndown chart for "%s"');
    }

    /**
     * Common method for CFD and Burdown chart
     *
     * @access private
     */
    private function commonAggregateMetrics($template, $column, $title)
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->projectDailyColumnStats->updateTotals($project['id'], date('Y-m-d'));

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $values['from'];
            $to = $values['to'];
        }

        $display_graph = $this->projectDailyColumnStats->countDays($project['id'], $from, $to) >= 2;

        $this->response->html($this->layout($template, array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'display_graph' => $display_graph,
            'metrics' => $display_graph ? $this->projectDailyColumnStats->getAggregatedMetrics($project['id'], $from, $to, $column) : array(),
            'project' => $project,
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t($title, $project['name']),
        )));
    }
}
