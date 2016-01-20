<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Model\Transition;
use Kanboard\Model\Task;
use Kanboard\Model\TaskFinder;
use Kanboard\Analytic\AverageTimeSpentColumnAnalytic;

class AverageTimeSpentColumnAnalyticTest extends Base
{
    public function testAverageWithNoTransitions()
    {
        $taskCreationModel = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $averageLeadCycleTimeAnalytic = new AverageTimeSpentColumnAnalytic($this->container);
        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_completed' => $now + 3600));
        $this->container['db']->table(Task::TABLE)->eq('id', 2)->update(array('date_completed' => $now + 1800));

        $stats = $averageLeadCycleTimeAnalytic->build(1);
        $expected = array(
            1 => array(
                'count' => 2,
                'time_spent' => 3600+1800,
                'average' => (int) ((3600+1800)/2),
                'title' => 'Backlog',
            ),
            2 => array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => 'Ready',
            ),
            3 => array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => 'Work in progress',
            ),
            4 => array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => 'Done',
            )
        );

        $this->assertEquals($expected, $stats);
    }

    public function testAverageWithTransitions()
    {
        $transitionModel = new Transition($this->container);
        $taskFinderModel = new TaskFinder($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $averageLeadCycleTimeAnalytic = new AverageTimeSpentColumnAnalytic($this->container);
        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['db']->table(Task::TABLE)->eq('id', 1)->update(array('date_completed' => $now + 3600));
        $this->container['db']->table(Task::TABLE)->eq('id', 2)->update(array('date_completed' => $now + 1800));

        foreach (array(1, 2) as $task_id) {
            $task = $taskFinderModel->getById($task_id);
            $task['task_id'] = $task['id'];
            $task['date_moved'] = $now - 900;
            $task['src_column_id'] = 3;
            $task['dst_column_id'] = 1;
            $this->assertTrue($transitionModel->save(1, $task));
        }

        $stats = $averageLeadCycleTimeAnalytic->build(1);
        $expected = array(
            1 => array(
                'count' => 2,
                'time_spent' => 3600+1800,
                'average' => (int) ((3600+1800)/2),
                'title' => 'Backlog',
            ),
            2 => array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => 'Ready',
            ),
            3 => array(
                'count' => 2,
                'time_spent' => 1800,
                'average' => 900,
                'title' => 'Work in progress',
            ),
            4 => array(
                'count' => 0,
                'time_spent' => 0,
                'average' => 0,
                'title' => 'Done',
            )
        );

        $this->assertEquals($expected, $stats);
    }
}
