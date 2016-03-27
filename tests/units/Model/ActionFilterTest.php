<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Model\ActivityFilter;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Core\DateParser;
use Kanboard\Model\ProjectActivity;
use Kanboard\Model\Comment;

class ActionFilterTest extends Base
{
    public function testSearchWithEmptyResult()
    {
        $e = new ProjectActivity($this->container);
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'date_due' => $dp->getTimestampFromIsoFormat('-2 days'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'date_due' => $dp->getTimestampFromIsoFormat('+1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'date_due' => $dp->getTimestampFromIsoFormat('-1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'youpi', 'date_due' => $dp->getTimestampFromIsoFormat(time()))));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertFalse($e->createEvent(1, 1, 0, Task::EVENT_OPEN, array('task' => $tf->getbyId(1))));

        $this->assertEmpty($af->search('search something')->findAll());
    }

    public function testSearchWithEmptyInput()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #2', 'project_id' => 1)));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertFalse($e->createEvent(1, 1, 0, Task::EVENT_OPEN, array('task' => $tf->getbyId(1))));

        $result = $af->search('')->findAll();

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
    }

    public function testFilterByTask()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task #5')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task 7')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task 43')));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertTrue($e->createEvent(1, 3, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(3))));

        $af->search('Task');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(3, $activities);
        $this->assertEquals('Task #5', $activities[0]['title']);

        $af->search('Task 7');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task 7', $activities[0]['title']);

        $af->search('#3');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task 43', $activities[0]['title']);

        $af->search('ask #');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #5', $activities[0]['title']);

        $af->search('#');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #5', $activities[0]['title']);
    }

    public function testFilterByCreator()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(2, $u->create(array('username' => 'bob', 'name' => 'Bob Ryan')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'owner_id' => 1)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'owner_id' => 0)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'owner_id' => 2)));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_UPDATE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 2, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertTrue($e->createEvent(1, 3, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(3))));

        $af->search('creator:john');
        $activities = $af->findAll();
        $this->assertEmpty($activities);

        $af->search('creator:admin my task title');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('my task title is awesome', $activities[0]['title']);

        $af->search('my task title');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(2, $activities);
        $this->assertEquals('my task title is awesome', $activities[0]['title']);
        $this->assertEquals('my task title is amazing', $activities[1]['title']);

        $af->search('my task title creator:admin');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('my task title is awesome', $activities[0]['title']);

        $af->search('creator:"Bob ryan" creator:admin');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(3, $activities);
        $this->assertEquals('my task title is awesome', $activities[0]['title']);
        $this->assertEquals('my task title is amazing', $activities[1]['title']);
    }

    public function testSearchWithStatus()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'is_active' => 0)));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_OPEN, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_CLOSE, array('task' => $tf->getById(2))));
        $this->assertTrue($e->createEvent(1, 3, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(3))));

        $af->search('status:open');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(2, $activities);

        $af->search('status:plop');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(3, $activities);

        $af->search('status:closed');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
    }

    public function testSearchWithProject()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'My project A')));
        $this->assertEquals(2, $p->create(array('name' => 'My project B')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 2, 'title' => 'task2')));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_UPDATE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(2, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getbyId(2))));

        $af->search('project:"My project A"');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(2, $activities);
        $this->assertEquals('task1', $activities[0]['title']);
        $this->assertEquals(1, $activities[0]['project_id']);

        $af->search('project:2');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('task2', $activities[0]['title']);
        $this->assertEquals(2, $activities[0]['project_id']);

        $af->search('project:"My project A" project:"my project b"');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(3, $activities);
        $this->assertEquals('task1', $activities[0]['title']);
        $this->assertEquals(1, $activities[0]['project_id']);
        $this->assertEquals('task2', $activities[2]['title']);
        $this->assertEquals(2, $activities[2]['project_id']);

        $af->search('project:"not found"');
        $activities = $af->findAll();
        $this->assertEmpty($activities);
    }

    public function testSearchByTaskId()
    {
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task #1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task #2')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task 43')));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(2))));
        $this->assertTrue($e->createEvent(1, 3, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(3))));

        $af->search('#2');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #2', $activities[0]['title']);

        $af->search('1');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #1', $activities[0]['title']);

        $af->search('something');
        $activities = $af->findAll();
        $this->assertEmpty($activities);

        $af->search('@');
        $activities = $af->findAll();
        $this->assertEmpty($activities);

        $af->search('#abcd');
        $activities = $af->findAll();
        $this->assertEmpty($activities);

        $af->search('Task #1');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #1', $activities[0]['title']);

        $af->search('43');
        $activities = $af->findAll();

        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task 43', $activities[0]['title']);
    }

    public function testFilterByComment()
    {
        $c = new Comment($this->container);
        $e = new ProjectActivity($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $af = new ActivityFilter($this->container);
        $tf = new TaskFinder($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task #1')));
        $this->assertEquals(1, $c->create(array('task_id' => 1, 'comment' => 'bla bla', 'user_id' => 1)));
        $this->assertEquals(2, $c->create(array('task_id' => 1, 'comment' => 'bla alb')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Task #2')));
        $this->assertEquals(3, $c->create(array('task_id' => 2, 'comment' => 'bla bla', 'user_id' => 1)));
        $this->assertEquals(4, $c->create(array('task_id' => 2, 'comment' => 'c2')));

        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_UPDATE, array('task' => $tf->getById(1))));
        $this->assertTrue($e->createEvent(1, 1, 1, Task::EVENT_CLOSE, array('task' => $tf->getbyId(1))));
        $this->assertTrue($e->createEvent(1, 2, 1, Task::EVENT_UPDATE, array('task' => $tf->getbyId(2))));

        $af->search('comment:bla');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(5, $activities);
        $this->assertEquals('Task #1', $activities[0]['title']);

        $af->search('comment:alb');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(2, $activities);
        $this->assertEquals('Task #1', $activities[0]['title']);

        $af->search('comment:alb');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(2, $activities);
        $this->assertEquals('Task #1', $activities[0]['title']);

        $af->search('comment:c2');
        $activities = $af->findAll();
        $this->assertNotEmpty($activities);
        $this->assertCount(1, $activities);
        $this->assertEquals('Task #2', $activities[0]['title']);
    }

    public function testCopy()
    {
        $af = new ActivityFilter($this->container);
        $filter1 = $af->create();
        $filter2 = $af->copy();

        $this->assertTrue($filter1 !== $filter2);
        $this->assertTrue($filter1->query !== $filter2->query);
        $this->assertTrue($filter1->query->condition !== $filter2->query->condition);
    }
}
