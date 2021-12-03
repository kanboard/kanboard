<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Column Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ColumnController extends BaseController
{
    /**
     * Display columns list
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();
        $columns = $this->columnModel->getAllWithTaskCount($project['id']);

        $this->response->html($this->helper->layout->project('column/index', array(
            'columns' => $columns,
            'project' => $project,
            'title' => t('Edit columns')
        )));
    }

    /**
     * Show form to create a new column
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values = array('project_id' => $project['id']);
        }

        $this->response->html($this->template->render('column/create', array(
            'values' => $values,
            'errors' => $errors,
            'project' => $project,
        )));
    }

    /**
     * Validate and add a new column
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues() + array('hide_in_dashboard' => 0);
        $values['project_id'] = $project['id'];

        list($valid, $errors) = $this->columnValidator->validateCreation($values);

        if ($valid) {
            $result = $this->columnModel->create(
                $project['id'],
                $values['title'],
                $values['task_limit'],
                $values['description'],
                $values['hide_in_dashboard']
            );

            if ($result !== false) {
                $this->flash->success(t('Column created successfully.'));
                $this->response->redirect($this->helper->url->to('ColumnController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $errors['title'] = array(t('Another column with the same name exists in the project'));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Display a form to edit a column
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $column = $this->getColumn($project);

        $this->response->html($this->helper->layout->project('column/edit', array(
            'errors' => $errors,
            'values' => $values ?: $column,
            'project' => $project,
            'column' => $column,
        )));
    }

    /**
     * Validate and update a column
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();
        $column = $this->getColumn($project);

        $values = $this->request->getValues() + array('hide_in_dashboard' => 0);
        $values['project_id'] = $project['id'];
        $values['id'] = $column['id'];

        list($valid, $errors) = $this->columnValidator->validateModification($values);

        if ($valid) {
            $result = $this->columnModel->update(
                $values['id'],
                $values['title'],
                $values['task_limit'],
                $values['description'],
                $values['hide_in_dashboard']
            );

            if ($result) {
                $this->flash->success(t('Board updated successfully.'));
                $this->response->redirect($this->helper->url->to('ColumnController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $this->flash->failure(t('Unable to update this board.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Move column position
     *
     * @access public
     */
    public function move()
    {
        $this->checkReusableGETCSRFParam();
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (! empty($values) && isset($values['column_id']) && isset($values['position'])) {
            $result = $this->columnModel->changePosition($project['id'], $values['column_id'], $values['position']);
            $this->response->json(array('result' => $result));
        } else {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Confirm column suppression
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $column = $this->getColumn($project);

        $this->response->html($this->helper->layout->project('column/remove', array(
            'column' => $column,
            'project' => $project,
        )));
    }

    /**
     * Remove a column
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $column = $this->getColumn($project);

        if ($this->columnModel->remove($column['id'])) {
            $this->flash->success(t('Column removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this column.'));
        }

        $this->response->redirect($this->helper->url->to('ColumnController', 'index', array('project_id' => $project['id'])));
    }
}
