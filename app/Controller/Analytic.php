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
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());
        $params['analytic_content_for_layout'] = $this->template->load($template, $params);

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
        $metrics = $this->projectAnalytic->getTaskRepartition($project['id']);

        if ($this->request->isAjax()) {
            $this->response->json(array(
                'metrics' => $metrics,
                'labels' => array(
                    'column_title' => t('Column'),
                    'nb_tasks' => t('Number of tasks'),
                )
            ));
        }
        else {
            $this->response->html($this->layout('analytic/tasks', array(
                'project' => $project,
                'metrics' => $metrics,
                'title' => t('Task repartition for "%s"', $project['name']),
            )));
        }
    }

    /**
     * Show users repartition
     *
     * @access public
     */
    public function users()
    {
        $project = $this->getProject();
        $metrics = $this->projectAnalytic->getUserRepartition($project['id']);

        if ($this->request->isAjax()) {
            $this->response->json(array(
                'metrics' => $metrics,
                'labels' => array(
                    'user' => t('User'),
                    'nb_tasks' => t('Number of tasks'),
                )
            ));
        }
        else {
            $this->response->html($this->layout('analytic/users', array(
                'project' => $project,
                'metrics' => $metrics,
                'title' => t('User repartition for "%s"', $project['name']),
            )));
        }
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

        if ($this->request->isAjax()) {
            $this->response->json(array(
                'columns' => array_values($this->board->getColumnsList($project['id'])),
                'metrics' => $this->projectDailySummary->getRawMetrics($project['id'], $from, $to),
                'labels' => array(
                    'column' => t('Column'),
                    'day' => t('Date'),
                    'total' => t('Tasks'),
                )
            ));
        }
        else {
            $this->response->html($this->layout('analytic/cfd', array(
                'values' => array(
                    'from' => $from,
                    'to' => $to,
                ),
                'display_graph' => $this->projectDailySummary->countDays($project['id'], $from, $to) >= 2,
                'project' => $project,
                'date_format' => $this->config->get('application_date_format'),
                'date_formats' => $this->dateParser->getAvailableFormats(),
                'title' => t('Cumulative flow diagram for "%s"', $project['name']),
            )));
        }
    }
}
