<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\Swimlane as SwimlaneModel;

/**
 * Swimlanes Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class SwimlaneController extends BaseController
{
    /**
     * Get the swimlane (common method between actions)
     *
     * @access private
     * @return array
     * @throws PageNotFoundException
     */
    private function getSwimlane()
    {
        $swimlane = $this->swimlane->getById($this->request->getIntegerParam('swimlane_id'));

        if (empty($swimlane)) {
            throw new PageNotFoundException();
        }

        return $swimlane;
    }

    /**
     * List of swimlanes for a given project
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('swimlane/index', array(
            'default_swimlane' => $this->swimlane->getDefault($project['id']),
            'active_swimlanes' => $this->swimlane->getAllByStatus($project['id'], SwimlaneModel::ACTIVE),
            'inactive_swimlanes' => $this->swimlane->getAllByStatus($project['id'], SwimlaneModel::INACTIVE),
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
        list($valid, $errors) = $this->swimlaneValidator->validateCreation($values);

        if ($valid) {
            if ($this->swimlane->create($values)) {
                $this->flash->success(t('Your swimlane have been created successfully.'));
                return $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
            } else {
                $errors = array('name' => array(t('Another swimlane with the same name exists in the project')));
            }
        }

        return $this->create($values, $errors);
    }

    /**
     * Edit default swimlane (display the form)
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function editDefault(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $swimlane = $this->swimlane->getDefault($project['id']);

        $this->response->html($this->helper->layout->project('swimlane/edit_default', array(
            'values' => empty($values) ? $swimlane : $values,
            'errors' => $errors,
            'project' => $project,
        )));
    }

    /**
     * Change the default swimlane
     *
     * @access public
     */
    public function updateDefault()
    {
        $project = $this->getProject();

        $values = $this->request->getValues() + array('show_default_swimlane' => 0);
        list($valid, $errors) = $this->swimlaneValidator->validateDefaultModification($values);

        if ($valid) {
            if ($this->swimlane->updateDefault($values)) {
                $this->flash->success(t('The default swimlane have been updated successfully.'));
                return $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])), true);
            } else {
                $this->flash->failure(t('Unable to update this swimlane.'));
            }
        }

        return $this->editDefault($values, $errors);
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
        $swimlane = $this->getSwimlane();

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

        $values = $this->request->getValues();
        list($valid, $errors) = $this->swimlaneValidator->validateModification($values);

        if ($valid) {
            if ($this->swimlane->update($values)) {
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
        $swimlane = $this->getSwimlane();

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
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        if ($this->swimlane->remove($project['id'], $swimlane_id)) {
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
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        if ($this->swimlane->disable($project['id'], $swimlane_id)) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Disable default swimlane
     *
     * @access public
     */
    public function disableDefault()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();

        if ($this->swimlane->disableDefault($project['id'])) {
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
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        if ($this->swimlane->enable($project['id'], $swimlane_id)) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('SwimlaneController', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Enable default swimlane
     *
     * @access public
     */
    public function enableDefault()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();

        if ($this->swimlane->enableDefault($project['id'])) {
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
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (! empty($values) && isset($values['swimlane_id']) && isset($values['position'])) {
            $result = $this->swimlane->changePosition($project['id'], $values['swimlane_id'], $values['position']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException();
        }
    }
}
