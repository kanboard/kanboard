<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Model\TaskFilter;
use Kanboard\Model\TaskCreation;
use Kanboard\Core\DateParser;
use Kanboard\Model\Category;
use Kanboard\Model\Subtask;
use Kanboard\Model\Config;
use Kanboard\Model\Swimlane;

class TaskFilterTest extends Base
{
    public function testSearchWithEmptyResult()
    {
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'date_due' => $dp->getTimestampFromIsoFormat('-2 days'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'date_due' => $dp->getTimestampFromIsoFormat('+1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'date_due' => $dp->getTimestampFromIsoFormat('-1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'youpi', 'date_due' => $dp->getTimestampFromIsoFormat(time()))));

        $this->assertEmpty($tf->search('search something')->findAll());
    }

    public function testSearchWithEmptyInput()
    {
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'date_due' => $dp->getTimestampFromIsoFormat('-2 days'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'date_due' => $dp->getTimestampFromIsoFormat('+1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'date_due' => $dp->getTimestampFromIsoFormat('-1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'youpi', 'date_due' => $dp->getTimestampFromIsoFormat(time()))));

        $result = $tf->search('')->findAll();
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result);
    }

    public function testSearchById()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task 43')));

        $tf->search('#2');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);

        $tf->search('1');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);

        $tf->search('something');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('#');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('#abcd');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('task1');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);

        $tf->search('43');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task 43', $tasks[0]['title']);
    }

    public function testSearchWithReference()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2', 'reference' => 123)));

        $tf->search('ref:123');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);

        $tf->search('reference:123');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);

        $tf->search('ref:plop');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('ref:');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithStatus()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'is_active' => 0)));

        $tf->search('status:open');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);

        $tf->search('status:plop');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(3, $tasks);

        $tf->search('status:closed');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
    }

    public function testSearchWithDescription()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2', 'description' => '**something to do**')));

        $tf->search('description:"something"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);

        $tf->search('description:"rainy day"');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithCategory()
    {
        $p = new Project($this->container);
        $c = new Category($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $c->create(array('name' => 'Feature request', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'hé hé', 'project_id' => 1)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2', 'category_id' => 1)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task3', 'category_id' => 2)));

        $tf->search('category:"Feature request"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);
        $this->assertEquals('Feature request', $tasks[0]['category_name']);

        $tf->search('category:"hé hé"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task3', $tasks[0]['title']);
        $this->assertEquals('hé hé', $tasks[0]['category_name']);

        $tf->search('category:"Feature request" category:"hé hé"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);
        $this->assertEquals('Feature request', $tasks[0]['category_name']);
        $this->assertEquals('task3', $tasks[1]['title']);
        $this->assertEquals('hé hé', $tasks[1]['category_name']);

        $tf->search('category:none');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('', $tasks[0]['category_name']);

        $tf->search('category:"not found"');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithProject()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'My project A')));
        $this->assertEquals(2, $p->create(array('name' => 'My project B')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 2, 'title' => 'task2')));

        $tf->search('project:"My project A"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('My project A', $tasks[0]['project_name']);

        $tf->search('project:2');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);
        $this->assertEquals('My project B', $tasks[0]['project_name']);

        $tf->search('project:"My project A" project:"my project b"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('My project A', $tasks[0]['project_name']);
        $this->assertEquals('task2', $tasks[1]['title']);
        $this->assertEquals('My project B', $tasks[1]['project_name']);

        $tf->search('project:"not found"');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithSwimlane()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);
        $s = new Swimlane($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'My project A')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'Version 1.1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'Version 1.2')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1', 'swimlane_id' => 1)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2', 'swimlane_id' => 2)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task3', 'swimlane_id' => 0)));

        $tf->search('swimlane:"Version 1.1"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('Version 1.1', $tasks[0]['swimlane_name']);

        $tf->search('swimlane:"versioN 1.2"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);
        $this->assertEquals('Version 1.2', $tasks[0]['swimlane_name']);

        $tf->search('swimlane:"Default swimlane"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task3', $tasks[0]['title']);
        $this->assertEquals('Default swimlane', $tasks[0]['default_swimlane']);
        $this->assertEquals('', $tasks[0]['swimlane_name']);

        $tf->search('swimlane:default');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task3', $tasks[0]['title']);
        $this->assertEquals('Default swimlane', $tasks[0]['default_swimlane']);
        $this->assertEquals('', $tasks[0]['swimlane_name']);

        $tf->search('swimlane:"Version 1.1" swimlane:"Version 1.2"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('Version 1.1', $tasks[0]['swimlane_name']);
        $this->assertEquals('task2', $tasks[1]['title']);
        $this->assertEquals('Version 1.2', $tasks[1]['swimlane_name']);

        $tf->search('swimlane:"not found"');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithColumn()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'My project A')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task2', 'column_id' => 3)));

        $tf->search('column:Backlog');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('Backlog', $tasks[0]['column_name']);

        $tf->search('column:backlog column:"Work in progress"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('Backlog', $tasks[0]['column_name']);
        $this->assertEquals('task2', $tasks[1]['title']);
        $this->assertEquals('Work in progress', $tasks[1]['column_name']);

        $tf->search('column:"not found"');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);
    }

    public function testSearchWithDueDate()
    {
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'date_due' => $dp->getTimestampFromIsoFormat('-2 days'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'date_due' => $dp->getTimestampFromIsoFormat('+1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'date_due' => $dp->getTimestampFromIsoFormat('-1 day'))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'youpi', 'date_due' => $dp->getTimestampFromIsoFormat(time()))));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'no due date')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'due date at 0', 'date_due' => 0)));

        $tf->search('due:>'.date('Y-m-d'));
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);

        $tf->search('due:>='.date('Y-m-d'));
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);
        $this->assertEquals('youpi', $tasks[1]['title']);

        $tf->search('due:<'.date('Y-m-d'));
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('my task title is awesome', $tasks[0]['title']);
        $this->assertEquals('Bob at work', $tasks[1]['title']);

        $tf->search('due:<='.date('Y-m-d'));
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(3, $tasks);
        $this->assertEquals('my task title is awesome', $tasks[0]['title']);
        $this->assertEquals('Bob at work', $tasks[1]['title']);
        $this->assertEquals('youpi', $tasks[2]['title']);

        $tf->search('due:tomorrow');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);

        $tf->search('due:yesterday');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('Bob at work', $tasks[0]['title']);

        $tf->search('due:today');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('youpi', $tasks[0]['title']);
    }

    public function testSearchWithColor()
    {
        $p = new Project($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(2, $u->create(array('username' => 'bob', 'name' => 'Bob Ryan')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'color_id' => 'light_green')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'color_id' => 'blue')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work')));

        $tf->search('color:"Light Green"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is awesome', $tasks[0]['title']);

        $tf->search('color:"Light Green" amazing');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('color:"plop');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('color:unknown');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(3, $tasks);

        $tf->search('color:blue amazing');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);

        $tf->search('color:blue color:Yellow');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);
        $this->assertEquals('Bob at work', $tasks[1]['title']);
    }

    public function testSearchWithAssignee()
    {
        $p = new Project($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(2, $u->create(array('username' => 'bob', 'name' => 'Bob Ryan')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is awesome', 'owner_id' => 1)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'my task title is amazing', 'owner_id' => 0)));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'Bob at work', 'owner_id' => 2)));

        $tf->search('assignee:john');
        $tasks = $tf->findAll();
        $this->assertEmpty($tasks);

        $tf->search('assignee:admin my task title');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is awesome', $tasks[0]['title']);

        $tf->search('my task title');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('my task title is awesome', $tasks[0]['title']);
        $this->assertEquals('my task title is amazing', $tasks[1]['title']);

        $tf->search('my task title assignee:nobody');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);

        $tf->search('assignee:"Bob ryan" assignee:nobody');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('my task title is amazing', $tasks[0]['title']);
        $this->assertEquals('Bob at work', $tasks[1]['title']);
    }

    public function testSearchWithAssigneeIncludingSubtasks()
    {
        $p = new Project($this->container);
        $u = new User($this->container);
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $tf = new TaskFilter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(2, $u->create(array('username' => 'bob', 'name' => 'Paul Ryan')));

        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'task1', 'owner_id' => 2)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1, 'status' => 1, 'user_id' => 0)));

        $this->assertEquals(2, $tc->create(array('project_id' => 1, 'title' => 'task2', 'owner_id' => 0)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 2, 'status' => 1, 'user_id' => 2)));

        $this->assertEquals(3, $tc->create(array('project_id' => 1, 'title' => 'task3', 'owner_id' => 0)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 3, 'user_id' => 1)));

        $tf->search('assignee:bob');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('task2', $tasks[1]['title']);

        $tf->search('assignee:"Paul Ryan"');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task1', $tasks[0]['title']);
        $this->assertEquals('task2', $tasks[1]['title']);

        $tf->search('assignee:nobody');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(2, $tasks);
        $this->assertEquals('task2', $tasks[0]['title']);
        $this->assertEquals('task3', $tasks[1]['title']);

        $tf->search('assignee:admin');
        $tasks = $tf->findAll();
        $this->assertNotEmpty($tasks);
        $this->assertCount(1, $tasks);
        $this->assertEquals('task3', $tasks[0]['title']);
    }

    public function testCopy()
    {
        $tf = new TaskFilter($this->container);
        $filter1 = $tf->create();
        $filter2 = $tf->copy();

        $this->assertTrue($filter1 !== $filter2);
        $this->assertTrue($filter1->query !== $filter2->query);
        $this->assertTrue($filter1->query->condition !== $filter2->query->condition);
    }
}
