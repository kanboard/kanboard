<?php

use Kanboard\Model\UserModel;
use Kanboard\Pagination\UserPagination;

require_once __DIR__.'/../Base.php';

class UserPaginationTest extends Base
{
    public function testListingPagination()
    {
        $userModel = new UserModel($this->container);
        $userPagination = new UserPagination($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test1')));
        $this->assertEquals(3, $userModel->create(array('username' => 'test2')));

        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('id')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('username')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('name')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('email')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('role')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('twofactor_activated')->setDirection('DESC')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('is_ldap_user')->getCollection());
        $this->assertCount(3, $userPagination->getListingPaginator()->setOrder('is_active')->getCollection());
    }
}
