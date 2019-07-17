<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\CustomFilterModel;

class CustomFilterTest extends Base
{
    public function testCreation()
    {
        $p = new ProjectModel($this->container);
        $cf = new CustomFilterModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $cf->create(array('name' => 'My filter 1', 'filter' => 'status:open color:blue', 'project_id' => 1, 'user_id' => 1)));
        $this->assertEquals(2, $cf->create(array('name' => 'My filter 2', 'filter' => 'status:open color:red', 'project_id' => 1, 'user_id' => 1, 'is_shared' => 1)));
        $this->assertEquals(3, $cf->create(array('name' => 'My filter 3', 'filter' => 'status:open color:green', 'project_id' => 1, 'user_id' => 1, 'append' => 1)));

        $filter = $cf->getById(1);
        $this->assertNotEmpty($filter);
        $this->assertEquals('My filter 1', $filter['name']);
        $this->assertEquals('status:open color:blue', $filter['filter']);
        $this->assertEquals(1, $filter['project_id']);
        $this->assertEquals(1, $filter['user_id']);
        $this->assertEquals(0, $filter['is_shared']);
        $this->assertEquals(0, $filter['append']);

        $filter = $cf->getById(2);
        $this->assertNotEmpty($filter);
        $this->assertEquals('My filter 2', $filter['name']);
        $this->assertEquals('status:open color:red', $filter['filter']);
        $this->assertEquals(1, $filter['project_id']);
        $this->assertEquals(1, $filter['user_id']);
        $this->assertEquals(1, $filter['is_shared']);
        $this->assertEquals(0, $filter['append']);

        $filter = $cf->getById(3);
        $this->assertNotEmpty($filter);
        $this->assertEquals('My filter 3', $filter['name']);
        $this->assertEquals('status:open color:green', $filter['filter']);
        $this->assertEquals(1, $filter['project_id']);
        $this->assertEquals(1, $filter['user_id']);
        $this->assertEquals(0, $filter['is_shared']);
        $this->assertEquals(1, $filter['append']);
    }

    public function testModification()
    {
        $p = new ProjectModel($this->container);
        $cf = new CustomFilterModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $cf->create(array('name' => 'My filter 1', 'filter' => 'status:open color:blue', 'project_id' => 1, 'user_id' => 1)));
        $this->assertTrue($cf->update(array('id' => 1, 'filter' => 'color:red', 'is_shared' => 1)));

        $filter = $cf->getById(1);
        $this->assertNotEmpty($filter);
        $this->assertEquals('My filter 1', $filter['name']);
        $this->assertEquals('color:red', $filter['filter']);
        $this->assertEquals(1, $filter['project_id']);
        $this->assertEquals(1, $filter['user_id']);
        $this->assertEquals(1, $filter['is_shared']);
    }

    public function testGetAll()
    {
        $u = new UserModel($this->container);
        $p = new ProjectModel($this->container);
        $cf = new CustomFilterModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest 1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest 2')));

        $this->assertEquals(2, $u->create(array('username' => 'user 2')));

        $this->assertEquals(1, $cf->create(array('name' => 'My filter 1', 'filter' => 'color:blue', 'project_id' => 1, 'user_id' => 1)));
        $this->assertEquals(2, $cf->create(array('name' => 'My filter 2', 'filter' => 'color:red', 'project_id' => 1, 'user_id' => 1, 'is_shared' => 1)));
        $this->assertEquals(3, $cf->create(array('name' => 'My filter 3', 'filter' => 'color:green', 'project_id' => 1, 'user_id' => 2, 'is_shared' => 1)));
        $this->assertEquals(4, $cf->create(array('name' => 'My filter 4', 'filter' => 'color:brown', 'project_id' => 1, 'user_id' => 2, 'is_shared' => 0)));
        $this->assertEquals(5, $cf->create(array('name' => 'My filter 5', 'filter' => 'color:grey', 'project_id' => 2, 'user_id' => 2)));

        // Get filters for the project 1 and user 1
        $filters = $cf->getAll(1, 1);
        $this->assertCount(3, $filters);

        $this->assertEquals(1, $filters[0]['id']);
        $this->assertEquals('My filter 1', $filters[0]['name']);
        $this->assertEquals('color:blue', $filters[0]['filter']);
        $this->assertEquals(1, $filters[0]['project_id']);
        $this->assertEquals(1, $filters[0]['user_id']);
        $this->assertEquals(0, $filters[0]['is_shared']);
        $this->assertEquals('', $filters[0]['owner_name']);
        $this->assertEquals('admin', $filters[0]['owner_username']);

        $this->assertEquals(2, $filters[1]['id']);
        $this->assertEquals('My filter 2', $filters[1]['name']);
        $this->assertEquals('color:red', $filters[1]['filter']);
        $this->assertEquals(1, $filters[1]['project_id']);
        $this->assertEquals(1, $filters[1]['user_id']);
        $this->assertEquals(1, $filters[1]['is_shared']);
        $this->assertEquals('', $filters[1]['owner_name']);
        $this->assertEquals('admin', $filters[1]['owner_username']);

        $this->assertEquals(3, $filters[2]['id']);
        $this->assertEquals('My filter 3', $filters[2]['name']);
        $this->assertEquals('color:green', $filters[2]['filter']);
        $this->assertEquals(1, $filters[2]['project_id']);
        $this->assertEquals(2, $filters[2]['user_id']);
        $this->assertEquals(1, $filters[2]['is_shared']);
        $this->assertEquals('', $filters[2]['owner_name']);
        $this->assertEquals('user 2', $filters[2]['owner_username']);

        // Get filters for the project 1 and user 2
        $filters = $cf->getAll(1, 2);
        $this->assertCount(3, $filters);

        $this->assertEquals(2, $filters[0]['id']);
        $this->assertEquals('My filter 2', $filters[0]['name']);

        $this->assertEquals(3, $filters[1]['id']);
        $this->assertEquals('My filter 3', $filters[1]['name']);

        $this->assertEquals(4, $filters[2]['id']);
        $this->assertEquals('My filter 4', $filters[2]['name']);

        // Get filters for the project 2 and user 1
        $filters = $cf->getAll(2, 1);
        $this->assertCount(0, $filters);

        // Get filters for the project 2 and user 2
        $filters = $cf->getAll(2, 2);
        $this->assertCount(1, $filters);

        $this->assertEquals(5, $filters[0]['id']);
        $this->assertEquals('My filter 5', $filters[0]['name']);
        $this->assertEquals(0, $filters[0]['is_shared']);
    }

    public function testRemove()
    {
        $p = new ProjectModel($this->container);
        $cf = new CustomFilterModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $cf->create(array('name' => 'My filter 1', 'filter' => 'status:open color:blue', 'project_id' => 1, 'user_id' => 1)));

        $filters = $cf->getAll(1, 1);
        $this->assertNotEmpty($filters);

        $this->assertTrue($cf->remove(1));
        $this->assertFalse($cf->remove(1));

        $filters = $cf->getAll(1, 1);
        $this->assertEmpty($filters);
    }
}
