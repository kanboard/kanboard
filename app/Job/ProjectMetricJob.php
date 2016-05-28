<?php

namespace Kanboard\Job;

/**
 * Class ProjectMetricJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class ProjectMetricJob extends BaseJob
{
    /**
     * Set job parameters
     *
     * @access public
     * @param  integer $projectId
     * @return $this
     */
    public function withParams($projectId)
    {
        $this->jobParams = array($projectId);
        return $this;
    }

    /**
     * Execute job
     *
     * @access public
     * @param  integer $projectId
     */
    public function execute($projectId)
    {
        $this->logger->debug(__METHOD__.' Run project metrics calculation');
        $now = date('Y-m-d');

        $this->projectDailyColumnStatsModel->updateTotals($projectId, $now);
        $this->projectDailyStatsModel->updateTotals($projectId, $now);
    }
}
