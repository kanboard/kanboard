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
     * @param  bool   $withoutLayout
     * @param  string $message
     */
    public function accessForbidden($withoutLayout = false, $message = '')
    {
        if ($this->request->isAjax()) {
            $this->response->json(array('message' => $message ?: t('Access Forbidden')), 403);
        } else {
            $this->response->html($this->helper->layout->app('app/forbidden', array(
                'title' => t('Access Forbidden'),
                'no_layout' => $withoutLayout,
            )), 403);
        }
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
