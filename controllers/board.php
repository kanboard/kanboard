<?php

namespace Controller;

class Board extends Base
{
    // Display current board
    public function index()
    {
        $projects = $this->project->getListByStatus(\Model\Project::ACTIVE);

        if (! count($projects)) {
            $this->redirectNoProject();
        }
        else if (! empty($_SESSION['user']['default_project_id']) && isset($projects[$_SESSION['user']['default_project_id']])) {
            $project_id = $_SESSION['user']['default_project_id'];
            $project_name = $projects[$_SESSION['user']['default_project_id']];
        }
        else {
            list($project_id, $project_name) = each($projects);
        }

        $this->response->html($this->template->layout('board_index', array(
            'projects' => $projects,
            // FIXME: $project_id and $project_name might have not been defined
            'current_project_id' => $project_id,
            'current_project_name' => $project_name,
            'columns' => $this->board->get($project_id),
            'menu' => 'boards',
            'title' => $project_name
        )));
    }

    // Show a board
    public function show()
    {
        $projects = $this->project->getListByStatus(\Model\Project::ACTIVE);
        $project_id = $this->request->getIntegerParam('project_id');
        $project_name = $projects[$project_id];

        $this->response->html($this->template->layout('board_index', array(
            'projects' => $projects,
            'current_project_id' => $project_id,
            'current_project_name' => $project_name,
            'columns' => $this->board->get($project_id),
            'menu' => 'boards',
            'title' => $project_name
        )));
    }

    // Display a form to edit a board
    public function edit()
    {
        $this->checkPermissions();

        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->get($project_id);
        $columns = $this->board->getColumnsList($project_id);
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = $column_title;
        }

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => array(),
            'values' => $values + array('project_id' => $project_id),
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Edit board')
        )));
    }

    // Validate and update a board
    public function update()
    {
        $this->checkPermissions();

        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->get($project_id);
        $columns = $this->board->getColumnsList($project_id);
        $data = $this->request->getValues();
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = isset($data['title'][$column_id]) ? $data['title'][$column_id] : '';
        }

        list($valid, $errors) = $this->board->validateModification($columns, $values);

        if ($valid) {

            if ($this->board->update($data['title'])) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => $errors,
            'values' => $values + array('project_id' => $project_id),
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Edit board')
        )));
    }

    // Validate and add a new column
    public function add()
    {
        $this->checkPermissions();

        $project_id = $this->request->getIntegerParam('project_id');
        $project = $this->project->get($project_id);
        $columns = $this->board->getColumnsList($project_id);
        $data = $this->request->getValues();
        $values = array();

        foreach ($columns as $column_id => $column_title) {
            $values['title['.$column_id.']'] = $column_title;
        }

        list($valid, $errors) = $this->board->validateCreation($data);

        if ($valid) {

            if ($this->board->add($data)) {
                $this->session->flash(t('Board updated successfully.'));
                $this->response->redirect('?controller=board&action=edit&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update this board.'));
            }
        }

        $this->response->html($this->template->layout('board_edit', array(
            'errors' => $errors,
            'values' => $values + $data,
            'columns' => $columns,
            'project' => $project,
            'menu' => 'projects',
            'title' => t('Edit board')
        )));
    }

    // Confirmation dialog before removing a column
    public function confirm()
    {
        $this->checkPermissions();

        $this->response->html($this->template->layout('board_remove', array(
            'column' => $this->board->getColumn($this->request->getIntegerParam('column_id')),
            'menu' => 'projects',
            'title' => t('Remove a column from a board')
        )));
    }

    // Remove a column
    public function remove()
    {
        $this->checkPermissions();

        $column = $this->board->getColumn($this->request->getIntegerParam('column_id'));

        if ($column && $this->board->removeColumn($column['id'])) {
            $this->session->flash(t('Column removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this column.'));
        }

        $this->response->redirect('?controller=board&action=edit&project_id='.$column['project_id']);
    }

    // Save the board (Ajax request made by drag and drop)
    public function save()
    {
        $this->response->json(array(
            'result' => $this->board->saveTasksPosition($this->request->getValues())
        ));
    }
}
