<?php

namespace Controller;

/**
 * Column controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Column extends Base
{
    /**
     * Display columns list
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $columns = $this->board->getColumns($project['id']);

        foreach ($columns as $column) {
            $values['title['.$column['id'].']'] = $column['title'];
            $values['description['.$column['id'].']'] = $column['description'];
            $values['task_limit['.$column['id'].']'] = $column['task_limit'] ?: null;
        }

        $this->response->html($this->projectLayout('column/index', array(
            'errors' => $errors,
            'values' => $values + array('project_id' => $project['id']),
            'columns' => $columns,
            'project' => $project,
            'title' => t('Edit board')
        )));
    }

    /**
     * Validate and add a new column
     *
     * @access public
     */
    public function create()
    {
        $project = $this->getProject();
        $columns = $this->board->getColumnsList($project['id']);
        $data = $this->request->getValues();
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = $column_title;
        }

        list($valid, $errors) = $this->board->validateCreation($data);

        if ($valid) {

            if ($this->board->addColumn($project['id'], $data['title'], $data['task_limit'], $data['description'])) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect($this->helper->url('column', 'index', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->index($values, $errors);
    }

    /**
     * Display a form to edit a column
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $column = $this->board->getColumn($this->request->getIntegerParam('column_id'));

        $this->response->html($this->projectLayout('column/edit', array(
            'errors' => $errors,
            'values' => $values ?: $column,
            'project' => $project,
            'column' => $column,
            'title' => t('Edit column "%s"', $column['title'])
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
        $values = $this->request->getValues();

        list($valid, $errors) = $this->board->validateModification($values);

        if ($valid) {

            if ($this->board->updateColumn($values['id'], $values['title'], $values['task_limit'], $values['description'])) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect($this->helper->url('column', 'index', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Move a column up or down
     *
     * @access public
     */
    public function move()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $column_id = $this->request->getIntegerParam('column_id');
        $direction = $this->request->getStringParam('direction');

        if ($direction === 'up' || $direction === 'down') {
            $this->board->{'move'.$direction}($project['id'], $column_id);
        }

        $this->response->redirect($this->helper->url('column', 'index', array('project_id' => $project['id'])));
    }

    /**
     * Confirm column suppression
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('column/remove', array(
            'column' => $this->board->getColumn($this->request->getIntegerParam('column_id')),
            'project' => $project,
            'title' => t('Remove a column from a board')
        )));
    }

    /**
     * Remove a column
     *
     * @access public
     */
    public function remove()
    {
        $project = $this->getProject();
        $this->checkCSRFParam();
        $column = $this->board->getColumn($this->request->getIntegerParam('column_id'));

        if (! empty($column) && $this->board->removeColumn($column['id'])) {
            $this->session->flash(t('Column removed successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to remove this column.'));
        }

        $this->response->redirect($this->helper->url('column', 'index', array('project_id' => $project['id'])));
    }
}
