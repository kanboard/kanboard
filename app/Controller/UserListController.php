<?php

namespace Kanboard\Controller;

/**
 * Class User List Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserListController extends BaseController
{
    /**
     * List all users
     *
     * @access public
     */
    public function show()
    {
        $paginator = $this->userPagination->getListingPaginator();

        $this->response->html($this->helper->layout->app('user_list/listing', array(
            'title' => t('Users').' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
        )));
    }
}
