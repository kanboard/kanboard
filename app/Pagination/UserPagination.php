<?php

namespace Kanboard\Pagination;

use Kanboard\Core\Base;
use Kanboard\Core\Paginator;
use Kanboard\Model\UserModel;

/**
 * Class UserPagination
 *
 * @package Kanboard\Pagination
 * @author  Frederic Guillot
 */
class UserPagination extends Base
{
    /**
     * Get user listing pagination
     *
     * @access public
     * @return Paginator
     */
    public function getListingPaginator()
    {
        return $this->paginator
            ->setUrl('UserListController', 'show')
            ->setMax(30)
            ->setOrder(UserModel::TABLE.'.username')
            ->setQuery($this->userModel->getQuery())
            ->calculate();
    }
}
