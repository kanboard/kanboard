<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Swimlanes Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class SwimlaneController extends BaseController
{
    /**
     * List of swimlanes for a given project
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();
        $swimlanes = $this->swimlaneModel->getAllWithTaskCount($project['id']);

        $this->response->html($this->helper->layout->project('swimlane/index', array(
            'active_swimlanes' => $swimlanes['active'],
            'inactive_swimlanes' => $swimlanes['inactive'],
            'project' => $project,
            'title' => t('Swimlanes')
        )));
    }

    /**
     * Create a new swimlane
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('swimlane/create', array(
            'values' => $values + array('project_id' => $project['id']),
            'errors' => $errors,
            'project' => $project,
        )));
    }

    /**
     * Validate and save a new swimlane
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $values['project_id'] = $project['id'];

        list($valid, $errors) = $this->swimlaneValidator->validateCreation($values);

        if ($valid) {
            if ($this->swimlaneModel->create($project['id'], $values['name'], $values['description'], $values['task_limit']) !== false) {
                $this->flash->success(t('Your swimlane has been created successfully.'));
                $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $errors = array('name' => array(t('Another swimlane with the same name exists in the project')));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Edit a swimlane (display the form)
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);

        $this->response->html($this->helper->layout->project('swimlane/edit', array(
            'values' => empty($values) ? $swimlane : $values,
            'errors' => $errors,
            'project' => $project,
        )));
    }

    /**
     * Edit a swimlane (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);
        $values = $this->request->getValues();
        $values['project_id'] = $project['id'];
        $values['id'] = $swimlane['id'];

        list($valid, $errors) = $this->swimlaneValidator->validateModification($values);

        if ($valid) {
            if ($this->swimlaneModel->update($values['id'], $values)) {
                $this->flash->success(t('Swimlane updated successfully.'));
                return $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
            } else {
                $errors = array('name' => array(t('Another swimlane with the same name exists in the project')));
            }
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a swimlane
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);

        $this->response->html($this->helper->layout->project('swimlane/remove', array(
            'project' => $project,
            'swimlane' => $swimlane,
        )));
    }

    /**
     * Remove a swimlane
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);

        if ($this->swimlaneModel->remove($project['id'], $swimlane['id'])) {
            $this->flash->success(t('Swimlane removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Disable a swimlane
     *
     * @access public
     */
    public function disable()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);

        if ($this->swimlaneModel->disable($project['id'], $swimlane['id'])) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Enable a swimlane
     *
     * @access public
     */
    public function enable()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $swimlane = $this->getSwimlane($project);

        if ($this->swimlaneModel->enable($project['id'], $swimlane['id'])) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Move swimlane position
     *
     * @access public
     */
    public function move()
    {
        $this->checkReusableGETCSRFParam();
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (! empty($values) && isset($values['swimlane_id']) && isset($values['position'])) {
            $result = $this->swimlaneModel->changePosition($project['id'], $values['swimlane_id'], $values['position']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException();
        }
    }
}
