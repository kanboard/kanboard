<?php

namespace Kanboard\Controller;

/**
 * Atom/RSS Feed controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Feed extends Base
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
            $this->forbidden(true);
        }

        $projects = $this->projectPermission->getActiveMemberProjects($user['id']);

        $this->response->xml($this->template->render('feed/user', array(
            'events' => $this->projectActivity->getProjects(array_keys($projects)),
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

        // Token verification
        if (empty($project)) {
            $this->forbidden(true);
        }

        $this->response->xml($this->template->render('feed/project', array(
            'events' => $this->projectActivity->getProject($project['id']),
            'project' => $project,
        )));
    }
}
