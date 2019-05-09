<?php

use Kanboard\Filter\TaskCommentFilter;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

require_once __DIR__.'/../Base.php';

class TaskCommentFilterTest extends Base
{
    public function testMatch()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $commentModel = new CommentModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'user_id' => 1, 'comment' => 'This is a test')));
        $this->assertEquals(2, $commentModel->create(array('task_id' => 1, 'user_id' => 1, 'comment' => 'This is another test')));

        $filter = new TaskCommentFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('test');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testNoMatch()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $commentModel = new CommentModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $commentModel->create(array('task_id' => 1, 'user_id' => 1, 'comment' => 'This is a test')));

        $filter = new TaskCommentFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('foobar');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }
}
