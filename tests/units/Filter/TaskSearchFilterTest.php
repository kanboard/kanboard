<?php

namespace KanboardTests\units\Filter;

use KanboardTests\units\Base;
use Kanboard\Filter\TaskSearchFilter;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

class TaskSearchFilterTest extends Base
{
    public function testMatchTitleDescriptionAndComment()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $commentModel = new CommentModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'needle title', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Description task', 'description' => 'needle description', 'project_id' => 1)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Comment task', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 3, 'user_id' => 1, 'comment' => 'needle comment')));

        $filter = new TaskSearchFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('needle');
        $filter->apply();

        $taskIds = array_column($query->findAll(), 'id');
        sort($taskIds);

        $this->assertSame(array(1, 2, 3), $taskIds);
    }

    public function testMatchTaskId()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Task 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Task 2', 'project_id' => 1)));

        $filter = new TaskSearchFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('#2');
        $filter->apply();

        $tasks = $query->findAll();

        $this->assertCount(1, $tasks);
        $this->assertEquals(2, $tasks[0]['id']);
    }
}
