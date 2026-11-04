<?php

namespace KanboardTests\units\Filter;

use KanboardTests\units\Base;
use Kanboard\Filter\UserNameFilter;
use Kanboard\Model\UserModel;

class UserNameFilterTest extends Base
{
    public function testFilterByUsernameOrName()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'jdoe', 'name' => 'John Doe')));
        $this->assertEquals(3, $userModel->create(array('username' => 'msmith', 'name' => 'Mary Smith')));

        $this->assertSame(array('jdoe'), $this->findUsernames('jd'));
        $this->assertSame(array('msmith'), $this->findUsernames('smith'));
        $this->assertSame(array(), $this->findUsernames('nobody'));
    }

    public function testFilterByExactEmail()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'jdoe', 'email' => 'John.Doe@example.com')));
        $this->assertEquals(3, $userModel->create(array('username' => 'msmith', 'email' => 'mary@example.com')));

        $this->assertSame(array('jdoe'), $this->findUsernames('john.doe@example.com'));
        $this->assertSame(array('msmith'), $this->findUsernames('MARY@example.com'));
    }

    public function testFilterDoesNotMatchPartialEmail()
    {
        $userModel = new UserModel($this->container);
        $this->assertEquals(2, $userModel->create(array('username' => 'jdoe', 'email' => 'john@example.com')));

        $this->assertSame(array(), $this->findUsernames('@example.com'));
        $this->assertSame(array(), $this->findUsernames('john@'));
        $this->assertSame(array(), $this->findUsernames('example.com'));
        $this->assertSame(array(), $this->findUsernames('%@example.com'));
    }

    private function findUsernames($search)
    {
        $query = $this->container['db']->table(UserModel::TABLE);

        $filter = new UserNameFilter($search);
        $filter->withQuery($query);
        $filter->apply();

        return $query->asc('username')->findAllByColumn('username');
    }
}
