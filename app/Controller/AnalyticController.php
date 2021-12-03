<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;

/**
 * Project Analytic Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class AnalyticController extends BaseController
{
    /**
     * Show average Lead and Cycle time
     *
     * @access public
     */
    public function leadAndCycleTime()
    {
        $project = $this->getProject();
        list($from, $to) = $this->getDates();

        $this->response->html($this->helper->layout->analytic('analytic/lead_cycle_time', array(
            'values' => array(
                'from' => $from,
                'to' => $to,
            ),
            'project' => $project,
            'average' => $this->averageLeadCycleTimeAnalytic->build($project['id']),
            'metrics' => $this->projectDailyStatsModel->getRawMetrics($project['id'], $from, $to),
            'title' => t('Lead and cycle time'),
        )));
    }

    /**
     * Show comparison between actual and estimated hours chart
     *
     * @access public
     */
    public function timeComparison()
    {
        $project = $this->getProject();

        $paginator = $this->paginator
            ->setUrl('AnalyticController', 'timeComparison', array('project_id' => $project['id']))
            ->setMax(30)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($this->taskQuery
                ->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery()
            )
            ->calculate();

        $this->response->html($this->helper->layout->analytic('analytic/time_comparison', array(
            'project' => $project,
            'paginator' => $paginator,
            'metrics' => $this->estimatedTimeComparisonAnalytic->build($project['id']),
            'title' => t('Estimated vs actual time'),
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

        $this->response->html($this->helper->layout->analytic('analytic/avg_time_columns', array(
            'project' => $project,
            'metrics' => $this->averageTimeSpentColumnAnalytic->build($project['id']),
            'title' => t('Average time into each column'),
        )));
    }

    /**
     * Show tasks distribution graph
     *
     * @access public
     */
    public function taskDistribution()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->analytic('analytic/task_distribution', array(
            'project' => $project,
            'metrics' => $this->taskDistributionAnalytic->build($project['id']),
            'title' => t('Task distribution'),
        )));
    }

    /**
     * Show users repartition
     *
     * @access public
     */
    public function userDistribution()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->analytic('analytic/user_distribution', array(
            'project' => $project,
            'metrics' => $this->userDistributionAnalytic->build($project['id']),
            'title' => t('User repartition'),
        )));
    }

    /**
     * Show cumulative flow diagram
     *
     * @access public
     */
    public function cfd()
    {
        $this->commonAggregateMetrics('analytic/cfd', 'total', t('Cumulative flow diagram'));
    }

    /**
     * Show burndown chart
     *
     * @access public
     */
    public function burndown()
    {
        $this->commonAggregateMetrics('analytic/burndown', 'score', t('Burndown chart'));
    }

    /**
     * Estimated vs actual time per column
     *
     * @access public
     */
    public function estimatedVsActualByColumn()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->analytic('analytic/estimated_actual_column', array(
            'project' => $project,
            'metrics' => $this->estimatedActualColumnAnalytic->build($project['id']),
            'title' => t('Estimated vs actual time per column'),
        )));
    }

    /**
     * Common method for CFD and Burdown chart
     *
     * @access private
     * @param string $template
     * @param string $column
     * @param string $title
     */
    private function commonAggregateMetrics($template, $column, $title)
    {
        $project = $this->getProject();
        list($from, $to) = $this->getDates();

        $displayGraph = $this->projectDailyColumnStatsModel->countDays($project['id'], $from, $to) >= 2;
        $metrics = $displayGraph ? $this->projectDailyColumnStatsModel->getAggregatedMetrics($project['id'], $from, $to, $column) : array();

        $this->response->html($this->helper->layout->analytic($template, array(
            'values'        => array(
                'from' => $from,
                'to'   => $to,
            ),
            'display_graph' => $displayGraph,
            'metrics'       => $metrics,
            'project'       => $project,
            'title'         => $title,
        )));
    }

    private function getDates()
    {
        $values = $this->request->getValues();

        $from = $this->request->getStringParam('from', date('Y-m-d', strtotime('-1week')));
        $to = $this->request->getStringParam('to', date('Y-m-d'));

        if (! empty($values)) {
            $from = $this->dateParser->getIsoDate($values['from']);
            $to = $this->dateParser->getIsoDate($values['to']);
        }

        return array($from, $to);
    }
}
