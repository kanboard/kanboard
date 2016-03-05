<?php

namespace Kanboard\Controller;

/**
 * Duplicate automatic action from another project
 *
 * @package controller
 * @author  Frederic Guillot
 */
class ActionProject extends Base
{
    public function project()
    {
        $project = $this->getProject();
        $projects = $this->projectUserRole->getProjectsByUser($this->userSession->getId());
        unset($projects[$project['id']]);

        $this->response->html($this->template->render('action_project/project', array(
            'project' => $project,
            'projects_list' => $projects,
        )));
    }

    public function save()
    {
        $project = $this->getProject();
        $src_project_id = $this->request->getValue('src_project_id');

        if ($this->action->duplicate($src_project_id, $project['id'])) {
            $this->flash->success(t('Actions duplicated successfully.'));
        } else {
            $this->flash->failure(t('Unable to duplicate actions.'));
        }

        $this->response->redirect($this->helper->url->to('action', 'index', array('project_id' => $project['id'])));
    }
}
