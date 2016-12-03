<?php

namespace Kanboard\Controller;

use Kanboard\Filter\UserNameFilter;
use Kanboard\Formatter\UserAutoCompleteFormatter;
use Kanboard\Formatter\UserMentionFormatter;
use Kanboard\Model\UserModel;

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
        $query = $this->request->getStringParam('search');
        $users = $this->projectPermissionModel->findUsernames($project_id, $query);

        $this->response->json(
            UserMentionFormatter::getInstance($this->container)
                ->withUsers($users)
                ->format()
        );
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
