<?php

namespace Kanboard\Controller;

/**
 * User Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class UserHelper extends Base
{
    /**
     * User autocompletion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $users = $this->userFilterAutoCompleteFormatter->create($search)->filterByUsernameOrByName()->format();
        $this->response->json($users);
    }

    /**
     * User mention autocompletion (Ajax)
     *
     * @access public
     */
    public function mention()
    {
        $project_id = $this->request->getStringParam('project_id');
        $query = $this->request->getStringParam('q');
        $users = $this->projectPermission->findUsernames($project_id, $query);
        $this->response->json($users);
    }
}
