<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Atom/RSS Feed controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Feed extends BaseController
{
    /**
     * RSS feed for a user
     *
     * @access public
     */
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->user->getByToken($token);

        // Token verification
        if (empty($user)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $this->response->xml($this->template->render('feed/user', array(
            'events' => $this->helper->projectActivity->getProjectsEvents($this->projectPermission->getActiveProjectIds($user['id'])),
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
        $project = $this->project->getByToken($token);

        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $this->response->xml($this->template->render('feed/project', array(
            'events' => $this->helper->projectActivity->getProjectEvents($project['id']),
            'project' => $project,
        )));
    }
}
