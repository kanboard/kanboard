<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Class ColumnMoveRestrictionController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ColumnMoveRestrictionController extends BaseController
{
    /**
     * Show form to create a new column restriction
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

        $this->response->html($this->template->render('column_move_restriction/create', array(
            'project' => $project,
            'role' => $role,
            'columns' => $this->columnModel->getList($project['id']),
            'values' => $values + array('project_id' => $project['id'], 'role_id' => $role['role_id']),
            'errors' => $errors,
        )));
    }

    /**
     * Save new column restriction
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->columnMoveRestrictionValidator->validateCreation($values);

        if ($valid) {
            $restriction_id = $this->columnMoveRestrictionModel->create(
                $project['id'],
                $values['role_id'],
                $values['src_column_id'],
                $values['dst_column_id'],
                isset($values['only_assigned']) && $values['only_assigned'] == 1
            );

            if ($restriction_id !== false) {
                $this->flash->success(t('The column restriction has been created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create this column restriction.'));
            }

            $this->response->redirect($this->helper->url->to('ProjectRoleController', 'show', array('project_id' => $project['id'])));
        } else {
            $this->create($values, $errors);
        }
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

        $this->response->html($this->helper->layout->project('column_move_restriction/remove', array(
            'project' => $project,
            'restriction' => $this->columnMoveRestrictionModel->getById($project['id'], $restriction_id),
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

        if ($this->columnMoveRestrictionModel->remove($restriction_id)) {
            $this->flash->success(t('Column restriction removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this restriction.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectRoleController', 'show', array('project_id' => $project['id'])));
    }
}
