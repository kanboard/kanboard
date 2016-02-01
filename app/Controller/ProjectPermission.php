<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\Role;

/**
 * Project Permission
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class ProjectPermission extends Base
{
    /**
     * Permissions are only available for team projects
     *
     * @access protected
     * @param  integer      $project_id    Default project id
     * @return array
     */
    protected function getProject($project_id = 0)
    {
        $project = parent::getProject($project_id);

        if ($project['is_private'] == 1) {
            $this->forbidden();
        }

        return $project;
    }

    /**
     * Show all permissions
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['role'] = Role::PROJECT_MEMBER;
        }

        $this->response->html($this->helper->layout->project('project_permission/index', array(
            'project' => $project,
            'users' => $this->projectUserRole->getUsers($project['id']),
            'groups' => $this->projectGroupRole->getGroups($project['id']),
            'roles' => $this->role->getProjectRoles(),
            'values' => $values,
            'errors' => $errors,
            'title' => t('Project Permissions'),
        )));
    }

    /**
     * Allow everybody
     *
     * @access public
     */
    public function allowEverybody()
    {
        $project = $this->getProject();
        $values = $this->request->getValues() + array('is_everybody_allowed' => 0);

        if ($this->project->update($values)) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPermission', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Add user to the project
     *
     * @access public
     */
    public function addUser()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        if ($this->projectUserRole->addUser($values['project_id'], $values['user_id'], $values['role'])) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPermission', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Revoke user access
     *
     * @access public
     */
    public function removeUser()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $user_id = $this->request->getIntegerParam('user_id');

        if ($this->projectUserRole->removeUser($project['id'], $user_id)) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPermission', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Change user role
     *
     * @access public
     */
    public function changeUserRole()
    {
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (! empty($project) && ! empty($values) && $this->projectUserRole->changeUserRole($project['id'], $values['id'], $values['role'])) {
            $this->response->json(array('status' => 'ok'));
        } else {
            $this->response->json(array('status' => 'error'));
        }
    }

    /**
     * Add group to the project
     *
     * @access public
     */
    public function addGroup()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        if (empty($values['group_id']) && ! empty($values['external_id'])) {
            $values['group_id'] = $this->group->create($values['name'], $values['external_id']);
        }

        if ($this->projectGroupRole->addGroup($project['id'], $values['group_id'], $values['role'])) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPermission', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Revoke group access
     *
     * @access public
     */
    public function removeGroup()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $group_id = $this->request->getIntegerParam('group_id');

        if ($this->projectGroupRole->removeGroup($project['id'], $group_id)) {
            $this->flash->success(t('Project updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this project.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectPermission', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Change group role
     *
     * @access public
     */
    public function changeGroupRole()
    {
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (! empty($project) && ! empty($values) && $this->projectGroupRole->changeGroupRole($project['id'], $values['id'], $values['role'])) {
            $this->response->json(array('status' => 'ok'));
        } else {
            $this->response->json(array('status' => 'error'));
        }
    }
}
