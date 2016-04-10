<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Filter\LexerBuilder;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Model\Project;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;

class LexerBuilderTest extends Base
{
    public function testBuilderThatReturnResult()
    {
        $project = new Project($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $project->create(array('name' => 'Project')));
        $this->assertNotFalse($taskCreation->create(array('project_id' => 1, 'title' => 'Test')));

        $builder = new LexerBuilder();
        $builder->withFilter(new TaskAssigneeFilter());
        $builder->withFilter(new TaskTitleFilter(), true);
        $builder->withQuery($query);
        $tasks = $builder->build('assignee:nobody')->toArray();

        $this->assertCount(1, $tasks);
        $this->assertEquals('Test', $tasks[0]['title']);
    }

    public function testBuilderThatReturnNothing()
    {
        $project = new Project($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $project->create(array('name' => 'Project')));
        $this->assertNotFalse($taskCreation->create(array('project_id' => 1, 'title' => 'Test')));

        $builder = new LexerBuilder();
        $builder->withFilter(new TaskAssigneeFilter());
        $builder->withFilter(new TaskTitleFilter(), true);
        $builder->withQuery($query);
        $tasks = $builder->build('something')->toArray();

        $this->assertCount(0, $tasks);
    }

    public function testBuilderWithEmptyInput()
    {
        $project = new Project($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $project->create(array('name' => 'Project')));
        $this->assertNotFalse($taskCreation->create(array('project_id' => 1, 'title' => 'Test')));

        $builder = new LexerBuilder();
        $builder->withFilter(new TaskAssigneeFilter());
        $builder->withFilter(new TaskTitleFilter(), true);
        $builder->withQuery($query);
        $tasks = $builder->build('')->toArray();

        $this->assertCount(1, $tasks);
    }

    public function testBuilderWithMultipleMatches()
    {
        $project = new Project($this->container);
        $taskCreation = new TaskCreation($this->container);
        $taskFinder = new TaskFinder($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $project->create(array('name' => 'Project')));
        $this->assertNotFalse($taskCreation->create(array('project_id' => 1, 'title' => 'ABC', 'owner_id' => 1)));
        $this->assertNotFalse($taskCreation->create(array('project_id' => 1, 'title' => 'DEF')));

        $builder = new LexerBuilder();
        $builder->withFilter(new TaskAssigneeFilter());
        $builder->withFilter(new TaskTitleFilter(), true);
        $builder->withQuery($query);
        $tasks = $builder->build('assignee:nobody assignee:1')->toArray();

        $this->assertCount(2, $tasks);
    }

    public function testClone()
    {
        $taskFinder = new TaskFinder($this->container);
        $query = $taskFinder->getExtendedQuery();

        $builder = new LexerBuilder();
        $builder->withFilter(new TaskAssigneeFilter());
        $builder->withFilter(new TaskTitleFilter());
        $builder->withQuery($query);

        $clone = clone($builder);
        $this->assertFalse($builder === $clone);
        $this->assertFalse($builder->build('test')->getQuery() === $clone->build('test')->getQuery());
    }
}
