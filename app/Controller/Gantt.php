<?php

namespace Controller;

use Model\Task;

/**
 * Gantt controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Gantt extends Base
{
    /**
     * Show Gantt chart for projects
     */
    public function project()
    {
        $project = $this->getProject();
        $sorting = $this->request->getStringParam('sorting', 'board');
        $filter = $this->taskFilter->gantt()->filterByProject($project['id'])->filterByStatus(Task::STATUS_OPEN);

        if ($sorting === 'date') {
            $filter->query->asc(Task::TABLE.'.date_started')->asc(Task::TABLE.'.date_creation');
        }
        else {
            $filter->query->asc('column_position')->asc(Task::TABLE.'.position');
        }

        $this->response->html($this->template->layout('gantt/project', array(
            'sorting' => $sorting,
            'tasks' => $filter->toGanttBars(),
            'project' => $project,
            'title' => t('Gantt chart for %s', $project['name']),
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
    }

    /**
     * Save new task start date and due date
     */
    public function saveDate()
    {
        $project = $this->getProject();
        $values = $this->request->getJson();

        $result = $this->taskModification->update(array(
            'id' => $values['id'],
            'date_started' => strtotime($values['start']),
            'date_due' => strtotime($values['end']),
        ));

        if (! $result) {
            $this->response->json(array('message' => 'Unable to save task'), 400);
        }

        $this->response->json(array('message' => 'OK'), 201);
    }

    /**
     * Simplified form to create a new task
     *
     * @access public
     */
    public function task(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('gantt/task_creation', array(
            'errors' => $errors,
            'values' => $values + array(
                'project_id' => $project['id'],
                'column_id' => $this->board->getFirstColumn($project['id']),
                'position' => 1
            ),
            'users_list' => $this->projectPermission->getMemberList($project['id'], true, false, true),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'swimlanes_list' => $this->swimlane->getList($project['id'], false, true),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => $project['name'].' &gt; '.t('New task')
        )));
    }

    /**
     * Validate and save a new task
     *
     * @access public
     */
    public function saveTask()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if ($valid) {

            $task_id = $this->taskCreation->create($values);

            if ($task_id !== false) {
                $this->session->flash(t('Task created successfully.'));
                $this->response->redirect($this->helper->url->to('gantt', 'project', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to create your task.'));
            }
        }

        $this->task($values, $errors);
    }
}
