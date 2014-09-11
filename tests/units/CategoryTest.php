<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\Project;
use Model\Category;
use Model\User;

class CategoryTest extends Base
{
    public function testCreation()
    {
        $t = new Task($this->registry);
        $p = new Project($this->registry);
        $c = new Category($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'Category #2', 'project_id' => 1)));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1, 'category_id' => 2)));

        $task = $t->getById(1);
        $this->assertTrue(is_array($task));
        $this->assertEquals(2, $task['category_id']);

        $category = $c->getById(2);
        $this->assertTrue(is_array($category));
        $this->assertEquals(2, $category['id']);
        $this->assertEquals('Category #2', $category['name']);
        $this->assertEquals(1, $category['project_id']);
    }

    public function testRemove()
    {
        $t = new Task($this->registry);
        $p = new Project($this->registry);
        $c = new Category($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertEquals(2, $c->create(array('name' => 'Category #2', 'project_id' => 1)));
        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1, 'category_id' => 2)));

        $task = $t->getById(1);
        $this->assertTrue(is_array($task));
        $this->assertEquals(2, $task['category_id']);

        $this->assertTrue($c->remove(1));
        $this->assertTrue($c->remove(2));

        // Make sure tasks assigned with that category are reseted
        $task = $t->getById(1);
        $this->assertTrue(is_array($task));
        $this->assertEquals(0, $task['category_id']);
    }
}
