<?php

use Kanboard\Formatter\TaskListFormatter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskTagModel;

require_once __DIR__.'/../Base.php';

class TaskListFormatterTest extends Base
{
    public function testFormatWithTags()
    {
        $projectModel = new ProjectModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test1')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test2', 'column_id' => 3)));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test3')));

        $this->assertTrue($taskTagModel->save(1, 1, array('My tag 1', 'My tag 2')));
        $this->assertTrue($taskTagModel->save(1, 2, array('My tag 3')));

        $listing = TaskListFormatter::getInstance($this->container)
            ->withQuery($taskFinderModel->getExtendedQuery()->asc(TaskModel::TABLE.'.id'))
            ->format();

        $this->assertCount(3, $listing);

        $expected = array(
            array(
                'id' => 1,
                'name' => 'My tag 1',
                'task_id' => 1,
                'color_id' => null,
            ),
            array(
                'id' => 2,
                'name' => 'My tag 2',
                'task_id' => 1,
                'color_id' => null,
            ),
        );

        $this->assertEquals($expected, $listing[0]['tags']);

        $expected = array(
            array(
                'id' => 3,
                'name' => 'My tag 3',
                'task_id' => 2,
                'color_id' => null,
            ),
        );

        $this->assertEquals($expected, $listing[1]['tags']);
        $this->assertEquals(array(), $listing[2]['tags']);
    }
}
