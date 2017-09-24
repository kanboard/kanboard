<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Security\Role;

/**
 * Custom Filter Controller
 *
 * @package Kanboard\Controller
 * @author  Timo Litzbarski
 * @author  Frederic Guillot
 */
class CustomFilterController extends BaseController
{
    /**
     * Display list of filters
     *
     * @access public
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('custom_filter/index', array(
            'project' => $project,
            'custom_filters' => $this->customFilterModel->getAll($project['id'], $this->userSession->getId()),
            'title' => t('Custom filters'),
        )));
    }

    /**
     * Show creation form for custom filters
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('custom_filter/create', array(
            'values' => $values + array('project_id' => $project['id']),
            'errors' => $errors,
            'project' => $project,
        )));
    }

    /**
     * Save a new custom filter
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();

        $values = $this->request->getValues();
        $values['project_id'] = $project['id'];
        $values['user_id'] = $this->userSession->getId();

        list($valid, $errors) = $this->customFilterValidator->validateCreation($values);

        if ($valid) {
            if ($this->customFilterModel->create($values) !== false) {
                $this->flash->success(t('Your custom filter have been created successfully.'));
                $this->response->redirect($this->helper->url->to('CustomFilterController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $this->flash->failure(t('Unable to create your custom filter.'));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Confirmation dialog before removing a custom filter
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $filter = $this->getCustomFilter($project);

        $this->response->html($this->helper->layout->project('custom_filter/remove', array(
            'project' => $project,
            'filter' => $filter,
            'title' => t('Remove a custom filter')
        )));
    }

    /**
     * Remove a custom filter
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $filter = $this->getCustomFilter($project);

        $this->checkPermission($project, $filter);

        if ($this->customFilterModel->remove($filter['id'])) {
            $this->flash->success(t('Custom filter removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this custom filter.'));
        }

        $this->response->redirect($this->helper->url->to('CustomFilterController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Edit a custom filter (display the form)
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $filter = $this->customFilterModel->getById($this->request->getIntegerParam('filter_id'));

        $this->checkPermission($project, $filter);

        $this->response->html($this->helper->layout->project('custom_filter/edit', array(
            'values' => empty($values) ? $filter : $values,
            'errors' => $errors,
            'project' => $project,
            'filter' => $filter,
            'title' => t('Edit custom filter')
        )));
    }

    /**
     * Edit a custom filter (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();
        $filter = $this->customFilterModel->getById($this->request->getIntegerParam('filter_id'));

        $this->checkPermission($project, $filter);

        $values = $this->request->getValues();
        $values['id'] = $filter['id'];
        $values['project_id'] = $project['id'];

        if (! isset($values['is_shared'])) {
            $values += array('is_shared' => 0);
        }

        if (! isset($values['append'])) {
            $values += array('append' => 0);
        }

        list($valid, $errors) = $this->customFilterValidator->validateModification($values);

        if ($valid) {
            if ($this->customFilterModel->update($values)) {
                $this->flash->success(t('Your custom filter have been updated successfully.'));
                $this->response->redirect($this->helper->url->to('CustomFilterController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $this->flash->failure(t('Unable to update custom filter.'));
            }
        }

        $this->edit($values, $errors);
    }

    private function checkPermission(array $project, array $filter)
    {
        $user_id = $this->userSession->getId();

        if ($filter['user_id'] != $user_id && ($this->projectUserRoleModel->getUserRole($project['id'], $user_id) === Role::PROJECT_MANAGER || ! $this->userSession->isAdmin())) {
            throw new AccessForbiddenException();
        }
    }
}
