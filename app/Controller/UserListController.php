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
        $paginator = $this->paginator
            ->setUrl('UserListController', 'show')
            ->setMax(30)
            ->setOrder('username')
            ->setQuery($this->userModel->getQuery())
            ->calculate();

        $this->response->html($this->helper->layout->app('user_list/show', array(
            'title' => t('Users').' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
        )));
    }
}
