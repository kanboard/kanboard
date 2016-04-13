<?php

namespace Kanboard\Controller;

use Kanboard\Filter\UserNameFilter;
use Kanboard\Formatter\UserAutoCompleteFormatter;
use Kanboard\Model\User as UserModel;

/**
 * User Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class UserHelper extends Base
{
    /**
     * User auto-completion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $filter = $this->userQuery->withFilter(new UserNameFilter($search));
        $filter->getQuery()->asc(UserModel::TABLE.'.name')->asc(UserModel::TABLE.'.username');
        $this->response->json($filter->format(new UserAutoCompleteFormatter($this->container)));
    }

    /**
     * User mention auto-completion (Ajax)
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
