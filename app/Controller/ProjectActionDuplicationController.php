<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Duplicate automatic action from another project
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectActionDuplicationController extends BaseController
{
    public function show()
    {
        $project = $this->getProject();
        $projects = $this->projectUserRoleModel->getProjectsByUser($this->userSession->getId());
        unset($projects[$project['id']]);

        $this->response->html($this->template->render('project_action_duplication/show', array(
            'project' => $project,
            'projects_list' => $projects,
        )));
    }

    public function save()
    {
        $project = $this->getProject();
        $src_project_id = $this->request->getValue('src_project_id');

        if (empty($src_project_id) || ! $this->projectPermissionModel->isUserAllowed($src_project_id, $this->userSession->getId())) {
            throw new AccessForbiddenException();
        }

        if ($this->actionModel->duplicate($src_project_id, $project['id'])) {
            $this->flash->success(t('Actions duplicated successfully.'));
        } else {
            $this->flash->failure(t('Unable to duplicate actions.'));
        }

        $this->response->redirect($this->helper->url->to('ActionController', 'index', array('project_id' => $project['id'])));
    }
}
