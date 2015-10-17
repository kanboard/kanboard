<?php

namespace Kanboard\Controller;

use Kanboard\Model\User as UserModel;
use Kanboard\Model\Task as TaskModel;

/**
 * Project User overview
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Projectuser extends Base
{
    /**
     * Common layout for users overview views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());
        $params['content_for_sublayout'] = $this->template->render($template, $params);
        $params['filter'] = array('user_id' => $params['user_id']);

        return $this->template->layout('project_user/layout', $params);
    }

    private function common()
    {
        $user_id = $this->request->getIntegerParam('user_id', UserModel::EVERYBODY_ID);

        if ($this->userSession->isAdmin()) {
            $project_ids = $this->project->getAllIds();
        } else {
            $project_ids = $this->projectPermission->getMemberProjectIds($this->userSession->getId());
        }

        return array($user_id, $project_ids, $this->user->getList(true));
    }

    private function role($is_owner, $action, $title, $title_user)
    {
        list($user_id, $project_ids, $users) = $this->common();

        $query = $this->projectPermission->getQueryByRole($project_ids, $is_owner)->callback(array($this->project, 'applyColumnStats'));

        if ($user_id !== UserModel::EVERYBODY_ID) {
            $query->eq(UserModel::TABLE.'.id', $user_id);
            $title = t($title_user, $users[$user_id]);
        }

        $paginator = $this->paginator
            ->setUrl('projectuser', $action, array('user_id' => $user_id))
            ->setMax(30)
            ->setOrder('projects.name')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->layout('project_user/roles', array(
            'paginator' => $paginator,
            'title' => $title,
            'user_id' => $user_id,
            'users' => $users,
        )));
    }

    private function tasks($is_active, $action, $title, $title_user)
    {
        list($user_id, $project_ids, $users) = $this->common();

        $query = $this->taskFinder->getProjectUserOverviewQuery($project_ids, $is_active);

        if ($user_id !== UserModel::EVERYBODY_ID) {
            $query->eq(TaskModel::TABLE.'.owner_id', $user_id);
            $title = t($title_user, $users[$user_id]);
        }

        $paginator = $this->paginator
            ->setUrl('projectuser', $action, array('user_id' => $user_id))
            ->setMax(50)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->layout('project_user/tasks', array(
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
        $this->role(1, 'managers', t('People who are project managers'), 'Projects where "%s" is manager');
    }

    /**
     * Display the list of project members
     *
     */
    public function members()
    {
        $this->role(0, 'members', t('People who are project members'), 'Projects where "%s" is member');
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
}
