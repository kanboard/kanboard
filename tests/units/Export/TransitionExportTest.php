<?php

require_once __DIR__ . '/../Base.php';

use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TransitionModel;
use Kanboard\Export\TransitionExport;
use Kanboard\Model\ProjectModel;

class TransitionExportTest extends Base
{
    public function testExport()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $transitionModel = new TransitionModel($this->container);
        $transitionExportModel = new TransitionExport($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $task_event = array(
            'project_id' => 1,
            'task_id' => 1,
            'src_column_id' => 1,
            'dst_column_id' => 2,
            'date_moved' => time() - 3600
        );

        $this->assertTrue($transitionModel->save(1, $task_event));

        $export = $transitionExportModel->export(1, date('Y-m-d'), date('Y-m-d'));
        $this->assertCount(2, $export);

        $this->assertEquals(
            array('Id', 'Task Title', 'Source column', 'Destination column', 'Executer', 'Date', 'Time spent'),
            $export[0]
        );

        $this->assertEquals(
            array(1, 'test', 'Backlog', 'Ready', 'admin', date('m/d/Y H:i', time()), 1.0),
            $export[1]
        );
    }
}
