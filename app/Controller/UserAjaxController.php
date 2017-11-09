<?php

namespace Kanboard\Controller;

/**
 * User Ajax Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class UserAjaxController extends BaseController
{
    /**
     * User auto-completion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $users = $this->userManager->find($search);
        $this->response->json($this->userAutoCompleteFormatter->withUsers($users)->format());
    }

    /**
     * User mention auto-completion (Ajax)
     *
     * @access public
     */
    public function mention()
    {
        $project_id = $this->request->getStringParam('project_id');
        $query = $this->request->getStringParam('search');
        $users = $this->projectPermissionModel->findUsernames($project_id, $query);

        $this->response->json($this->userMentionFormatter->withUsers($users)->format());
    }

    /**
     * Check if the user is connected
     *
     * @access public
     */
    public function status()
    {
        $this->response->text('OK');
    }
}
