<?php

namespace Kanboard\Controller;

use Kanboard\Model\UserModel;
use Kanboard\Model\TaskModel;
use Kanboard\Core\Security\Role;

/**
 * Project User overview
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ProjectUserOverviewController extends BaseController
{
    private function common()
    {
        $user_id = $this->request->getIntegerParam('user_id', UserModel::EVERYBODY_ID);

        if ($this->userSession->isAdmin()) {
            $project_ids = $this->projectModel->getAllIds();
        } else {
            $project_ids = $this->projectPermissionModel->getActiveProjectIds($this->userSession->getId());
        }

        return array($user_id, $project_ids, $this->userModel->getActiveUsersList(true));
    }

    private function role($role, $action, $title, $title_user)
    {
        list($user_id, $project_ids, $users) = $this->common();

        $query = $this->projectPermissionModel->getQueryByRole($project_ids, $role)->callback(array($this->projectModel, 'applyColumnStats'));

        if ($user_id !== UserModel::EVERYBODY_ID && isset($users[$user_id])) {
            $query->eq(UserModel::TABLE.'.id', $user_id);
            $title = t($title_user, $users[$user_id]);
        }

        $paginator = $this->paginator
            ->setUrl('ProjectUserOverviewController', $action, array('user_id' => $user_id))
            ->setMax(30)
            ->setOrder('projects.name')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->projectUser('project_user_overview/roles', array(
            'paginator' => $paginator,
            'title' => $title,
            'user_id' => $user_id,
            'users' => $users,
        )));
    }

    private function tasks($is_active, $action, $title, $title_user)
    {
        list($user_id, $project_ids, $users) = $this->common();

        $query = $this->taskFinderModel->getProjectUserOverviewQuery($project_ids, $is_active);

        if ($user_id !== UserModel::EVERYBODY_ID && isset($users[$user_id])) {
            $query->eq(TaskModel::TABLE.'.owner_id', $user_id);
            $title = t($title_user, $users[$user_id]);
        }

        $paginator = $this->paginator
            ->setUrl('ProjectUserOverviewController', $action, array('user_id' => $user_id))
            ->setMax(50)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->projectUser('project_user_overview/tasks', array(
            'paginator' => $paginator,
            'title' => $title,
            'user_id' => $user_id,
            'users' => $users,
        )));
    }

    /**
     * Display the list of project managers
     *
     */
    public function managers()
    {
        $this->role(Role::PROJECT_MANAGER, 'managers', t('People who are project managers'), 'Projects where "%s" is manager');
    }

    /**
     * Display the list of project members
     *
     */
    public function members()
    {
        $this->role(Role::PROJECT_MEMBER, 'members', t('People who are project members'), 'Projects where "%s" is member');
    }

    /**
     * Display the list of open taks
     *
     */
    public function opens()
    {
        $this->tasks(TaskModel::STATUS_OPEN, 'opens', t('Open tasks'), 'Open tasks assigned to "%s"');
    }

    /**
     * Display the list of closed tasks
     *
     */
    public function closed()
    {
        $this->tasks(TaskModel::STATUS_CLOSED, 'closed', t('Closed tasks'), 'Closed tasks assigned to "%s"');
    }

    /**
     * Users tooltip
     */
    public function users()
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('project_user_overview/tooltip_users', array(
            'users' => $this->projectUserRoleModel->getAllUsersGroupedByRole($project['id']),
            'roles' => $this->projectRoleModel->getList($project['id']),
        )));
    }
}
