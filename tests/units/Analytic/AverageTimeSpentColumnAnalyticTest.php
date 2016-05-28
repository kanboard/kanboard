<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TransitionModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Analytic\AverageTimeSpentColumnAnalytic;

class AverageTimeSpentColumnAnalyticTest extends Base
{
    public function testAverageWithNoTransitions()
    {
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $averageLeadCycleTimeAnalytic = new AverageTimeSpentColumnAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $now = time();

        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('date_completed' => $now + 3600));
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 2)->update(array('date_completed' => $now + 1800));

        $stats = $averageLeadCycleTimeAnalytic->build(1);

        $this->assertEquals(2, $stats[1]['count']);
        $this->assertEquals(3600+1800, $stats[1]['time_spent'], '', 3);
        $this->assertEquals((int) ((3600+1800)/2), $stats[1]['average'], '', 3);
        $this->assertEquals('Backlog', $stats[1]['title']);

        $this->assertEquals(0, $stats[2]['count']);
        $this->assertEquals(0, $stats[2]['time_spent'], '', 3);
        $this->assertEquals(0, $stats[2]['average'], '', 3);
        $this->assertEquals('Ready', $stats[2]['title']);

        $this->assertEquals(0, $stats[3]['count']);
        $this->assertEquals(0, $stats[3]['time_spent'], '', 3);
        $this->assertEquals(0, $stats[3]['average'], '', 3);
        $this->assertEquals('Work in progress', $stats[3]['title']);

        $this->assertEquals(0, $stats[4]['count']);
        $this->assertEquals(0, $stats[4]['time_spent'], '', 3);
        $this->assertEquals(0, $stats[4]['average'], '', 3);
        $this->assertEquals('Done', $stats[4]['title']);
    }

    public function testAverageWithTransitions()
    {
        $transitionModel = new TransitionModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $averageLeadCycleTimeAnalytic = new AverageTimeSpentColumnAnalytic($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $now = time();
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 1)->update(array('date_completed' => $now + 3600));
        $this->container['db']->table(TaskModel::TABLE)->eq('id', 2)->update(array('date_completed' => $now + 1800));

        foreach (array(1, 2) as $task_id) {
            $task = $taskFinderModel->getById($task_id);
            $task['task_id'] = $task['id'];
            $task['date_moved'] = $now - 900;
            $task['src_column_id'] = 3;
            $task['dst_column_id'] = 1;
            $this->assertTrue($transitionModel->save(1, $task));
        }

        $stats = $averageLeadCycleTimeAnalytic->build(1);

        $this->assertEquals(2, $stats[1]['count']);
        $this->assertEquals(3600+1800, $stats[1]['time_spent'], '', 3);
        $this->assertEquals((int) ((3600+1800)/2), $stats[1]['average'], '', 3);
        $this->assertEquals('Backlog', $stats[1]['title']);

        $this->assertEquals(0, $stats[2]['count']);
        $this->assertEquals(0, $stats[2]['time_spent'], '', 3);
        $this->assertEquals(0, $stats[2]['average'], '', 3);
        $this->assertEquals('Ready', $stats[2]['title']);

        $this->assertEquals(2, $stats[3]['count']);
        $this->assertEquals(1800, $stats[3]['time_spent'], '', 3);
        $this->assertEquals(900, $stats[3]['average'], '', 3);
        $this->assertEquals('Work in progress', $stats[3]['title']);

        $this->assertEquals(0, $stats[4]['count']);
        $this->assertEquals(0, $stats[4]['time_spent'], '', 3);
        $this->assertEquals(0, $stats[4]['average'], '', 3);
        $this->assertEquals('Done', $stats[4]['title']);
    }
}
