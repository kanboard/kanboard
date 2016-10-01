<?php

namespace Kanboard\Controller;

use Kanboard\Core\Base;

/**
 * Class AppController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class AppController extends Base
{
    /**
     * Forbidden page
     *
     * @access public
     * @param  bool $withoutLayout
     */
    public function accessForbidden($withoutLayout = false)
    {
        if ($this->request->isAjax()) {
            $this->response->json(array('message' => 'Access Forbidden'), 403);
        }

        $this->response->html($this->helper->layout->app('app/forbidden', array(
            'title' => t('Access Forbidden'),
            'no_layout' => $withoutLayout,
        )));
    }

    /**
     * Page not found
     *
     * @access public
     * @param  boolean $withoutLayout
     */
    public function notFound($withoutLayout = false)
    {
        $this->response->html($this->helper->layout->app('app/notfound', array(
            'title' => t('Page not found'),
            'no_layout' => $withoutLayout,
        )));
    }
}
