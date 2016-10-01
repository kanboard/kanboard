<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Analytic\EstimatedTimeComparisonAnalytic;

class EstimatedTimeComparisonAnalyticTest extends Base
{
    public function testBuild()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $estimatedTimeComparisonAnalytic = new EstimatedTimeComparisonAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 5.5)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.75)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.25, 'is_active' => 0)));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 8.25)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 0.25)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 0.5, 'is_active' => 0)));

        $expected = array(
            'open' => array(
                'time_spent' => 8.5,
                'time_estimated' => 7.25,
            ),
            'closed' => array(
                'time_spent' => 0.5,
                'time_estimated' => 1.25,
            )
        );

        $this->assertEquals($expected, $estimatedTimeComparisonAnalytic->build(1));
    }

    public function testBuildWithNoClosedTask()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $estimatedTimeComparisonAnalytic = new EstimatedTimeComparisonAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 5.5)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.75)));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 8.25)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 0.25)));

        $expected = array(
            'open' => array(
                'time_spent' => 8.5,
                'time_estimated' => 7.25,
            ),
            'closed' => array(
                'time_spent' => 0,
                'time_estimated' => 0,
            )
        );

        $this->assertEquals($expected, $estimatedTimeComparisonAnalytic->build(1));
    }

    public function testBuildWithOnlyClosedTask()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $estimatedTimeComparisonAnalytic = new EstimatedTimeComparisonAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test1')));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 5.5, 'is_active' => 0)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_estimated' => 1.75, 'is_active' => 0)));

        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 8.25, 'is_active' => 0)));
        $this->assertNotFalse($taskCreationModel->create(array('project_id' => 1, 'title' => 'test', 'time_spent' => 0.25, 'is_active' => 0)));

        $expected = array(
            'closed' => array(
                'time_spent' => 8.5,
                'time_estimated' => 7.25,
            ),
            'open' => array(
                'time_spent' => 0,
                'time_estimated' => 0,
            )
        );

        $this->assertEquals($expected, $estimatedTimeComparisonAnalytic->build(1));
    }
}
