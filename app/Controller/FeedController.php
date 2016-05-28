<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Atom/RSS Feed controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class FeedController extends BaseController
{
    /**
     * RSS feed for a user
     *
     * @access public
     */
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->userModel->getByToken($token);

        // Token verification
        if (empty($user)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $this->response->xml($this->template->render('feed/user', array(
            'events' => $this->helper->projectActivity->getProjectsEvents($this->projectPermissionModel->getActiveProjectIds($user['id'])),
            'user' => $user,
        )));
    }

    /**
     * RSS feed for a project
     *
     * @access public
     */
    public function project()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->projectModel->getByToken($token);

        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $this->response->xml($this->template->render('feed/project', array(
            'events' => $this->helper->projectActivity->getProjectEvents($project['id']),
            'project' => $project,
        )));
    }
}
