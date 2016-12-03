<?php

use Kanboard\Formatter\TaskSuggestMenuFormatter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;

require_once __DIR__.'/../Base.php';

class TaskSuggestMenuFormatterTest extends Base
{
    public function testFormat()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskSuggestMenuFormatter = new TaskSuggestMenuFormatter($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'My Project')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task 2', 'project_id' => 1)));

        $result = $taskSuggestMenuFormatter
            ->withQuery($this->container['taskFinderModel']->getExtendedQuery())
            ->format()
        ;

        $expected = array(
            array(
                'value' => '1',
                'html' => '#1 Task 1 <small>My Project</small>',
            ),
            array(
                'value' => '2',
                'html' => '#2 Task 2 <small>My Project</small>',
            ),
        );

        $this->assertSame($expected, $result);
    }
}
