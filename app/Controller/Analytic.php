<?php

namespace Controller;

/**
 * Project Anaytic controller
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
        $project = $this->getProject();
        $values = $this->request->getValues();

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $values['from'];
            $to = $values['to'];
        }

        $display_graph = $this->projectDailySummary->countDays($project['id'], $from, $to) >= 2;

        $this->response->html($this->layout('analytic/cfd', array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'display_graph' => $display_graph,
            'metrics' => $display_graph ? $this->projectDailySummary->getAggregatedMetrics($project['id'], $from, $to) : array(),
            'project' => $project,
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t('Cumulative flow diagram for "%s"', $project['name']),
        )));
    }

    /**
     * Show burndown chart
     *
     * @access public
     */
    public function burndown()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $values['from'];
            $to = $values['to'];
        }

        $display_graph = $this->projectDailySummary->countDays($project['id'], $from, $to) >= 2;

        $this->response->html($this->layout('analytic/burndown', array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'display_graph' => $display_graph,
            'metrics' => $display_graph ? $this->projectDailySummary->getAggregatedMetrics($project['id'], $from, $to, 'score') : array(),
            'project' => $project,
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t('Burndown chart for "%s"', $project['name']),
        )));
    }
}
