<?php

use Kanboard\Job\ProjectMetricJob;

require_once __DIR__.'/../Base.php';

class ProjectMetricJobTest extends Base
{
    public function testJobParams()
    {
        $projectMetricJob = new ProjectMetricJob($this->container);
        $projectMetricJob->withParams(123);

        $this->assertSame(
            array(123),
            $projectMetricJob->getJobParams()
        );
    }

    public function testJob()
    {
        $this->container['projectDailyColumnStatsModel'] = $this
            ->getMockBuilder('\Kanboard\Model\ProjectDailyColumnStatsModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('updateTotals'))
            ->getMock();

        $this->container['projectDailyStatsModel'] = $this
            ->getMockBuilder('\Kanboard\Model\ProjectDailyStatsModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('updateTotals'))
            ->getMock();

        $this->container['projectDailyColumnStatsModel']
            ->expects($this->once())
            ->method('updateTotals')
            ->with(42, date('Y-m-d'));

        $this->container['projectDailyStatsModel']
            ->expects($this->once())
            ->method('updateTotals')
            ->with(42, date('Y-m-d'));

        $job = new ProjectMetricJob($this->container);
        $job->execute(42);
    }
}
