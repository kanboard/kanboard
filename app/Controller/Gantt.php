<?php

namespace Kanboard\Controller;

use Kanboard\Model\Task as TaskModel;

/**
 * Gantt controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Gantt extends Base
{
    /**
     * Show Gantt chart for all projects
     */
    public function projects()
    {
        if ($this->userSession->isAdmin()) {
            $project_ids = $this->project->getAllIds();
        } else {
            $project_ids = $this->projectPermission->getMemberProjectIds($this->userSession->getId());
        }

        $this->response->html($this->template->layout('gantt/projects', array(
            'projects' => $this->projectGanttFormatter->filter($project_ids)->format(),
            'title' => t('Gantt chart for all projects'),
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
    }

    /**
     * Save new project start date and end date
     */
    public function saveProjectDate()
    {
        $values = $this->request->getJson();

        $result = $this->project->update(array(
            'id' => $values['id'],
            'start_date' => $this->dateParser->getIsoDate(strtotime($values['start'])),
            'end_date' => $this->dateParser->getIsoDate(strtotime($values['end'])),
        ));

        if (! $result) {
            $this->response->json(array('message' => 'Unable to save project'), 400);
        }

        $this->response->json(array('message' => 'OK'), 201);
    }

    /**
     * Show Gantt chart for one project
     */
    public function project()
    {
        $params = $this->getProjectFilters('gantt', 'project');
        $filter = $this->taskFilterGanttFormatter->search($params['filters']['search'])->filterByProject($params['project']['id']);
        $sorting = $this->request->getStringParam('sorting', 'board');

        if ($sorting === 'date') {
            $filter->getQuery()->asc(TaskModel::TABLE.'.date_started')->asc(TaskModel::TABLE.'.date_creation');
        } else {
            $filter->getQuery()->asc('column_position')->asc(TaskModel::TABLE.'.position');
        }

        $this->response->html($this->template->layout('gantt/project', $params + array(
            'users_list' => $this->projectPermission->getMemberList($params['project']['id'], false),
            'sorting' => $sorting,
            'tasks' => $filter->format(),
        )));
    }

    /**
     * Save new task start date and due date
     */
    public function saveTaskDate()
    {
        $this->getProject();
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
            } else {
                $this->session->flashError(t('Unable to create your task.'));
            }
        }

        $this->task($values, $errors);
    }
}
