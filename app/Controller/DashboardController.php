<?php

namespace Kanboard\Controller;

/**
 * Dashboard Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class DashboardController extends BaseController
{
    /**
     * Dashboard overview
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->app('dashboard/show', array(
            'title'   => t('Dashboard for %s', $this->helper->user->getFullname($user)),
            'user'    => $user,
            'results' => $this->dashboardPagination->getOverview($user['id']),
        )));
    }
}
