<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Class ProjectRoleRestrictionController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectRoleRestrictionController extends BaseController
{
    /**
     * Show form to create a new project restriction
     *
     * @param  array $values
     * @param  array $errors
     * @throws AccessForbiddenException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $role_id = $this->request->getIntegerParam('role_id');
        $role = $this->projectRoleModel->getById($project['id'], $role_id);

        $this->response->html($this->template->render('project_role_restriction/create', array(
            'project' => $project,
            'role' => $role,
            'values' => $values + array('project_id' => $project['id'], 'role_id' => $role['role_id']),
            'errors' => $errors,
            'restrictions' => $this->projectRoleRestrictionModel->getRules(),
        )));
    }

    /**
     * Save new restriction
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $restriction_id = $this->projectRoleRestrictionModel->create(
            $project['id'],
            $values['role_id'],
            $values['rule']
        );

        if ($restriction_id !== false) {
            $this->flash->success(t('The project restriction has been created successfully.'));
        } else {
            $this->flash->failure(t('Unable to create this project restriction.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectRoleController', 'show', array('project_id' => $project['id'])));
    }

    /**
     * Confirm suppression
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $restriction_id = $this->request->getIntegerParam('restriction_id');

        $this->response->html($this->helper->layout->project('project_role_restriction/remove', array(
            'project' => $project,
            'restriction' => $this->projectRoleRestrictionModel->getById($project['id'], $restriction_id),
            'restrictions' => $this->projectRoleRestrictionModel->getRules(),
        )));
    }

    /**
     * Remove a restriction
     *
     * @access public
     */
    public function remove()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();
        $restriction_id = $this->request->getIntegerParam('restriction_id');

        if ($this->projectRoleRestrictionModel->remove($restriction_id)) {
            $this->flash->success(t('Project restriction removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this restriction.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectRoleController', 'show', array('project_id' => $project['id'])));
    }
}
