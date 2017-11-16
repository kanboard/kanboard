<?php

namespace Kanboard\Controller;

use Kanboard\Filter\UserNameFilter;

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
            'values' => array(),
        )));
    }

    /**
     * Search in users
     *
     * @access public
     */
    public function search()
    {
        $search = $this->request->getStringParam('search');
        $paginator = $this->userPagination->getListingPaginator();

        if ($search !== '' && ! $paginator->isEmpty()) {
            $paginator = $paginator
                ->setUrl('UserListController', 'search', array('search' => $search))
                ->setQuery($this->userQuery
                    ->withFilter(new UserNameFilter($search))
                    ->getQuery()
                )
                ->calculate();
        }

        $this->response->html($this->helper->layout->app('user_list/listing', array(
            'title' => t('Users').' ('.$paginator->getTotal().')',
            'values' => array(
                'search' => $search,
            ),
            'paginator' => $paginator
        )));
    }
}
