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
            $project_ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());
        }

        $this->response->html($this->helper->layout->app('gantt/projects', array(
            'projects' => $this->projectGanttFormatter->filter($project_ids)->format(),
            'title' => t('Gantt chart for all projects'),
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
        $project = $this->getProject();
        $search = $this->helper->projectHeader->getSearchQuery($project);
        $filter = $this->taskFilterGanttFormatter->search($search)->filterByProject($project['id']);
        $sorting = $this->request->getStringParam('sorting', 'board');

        if ($sorting === 'date') {
            $filter->getQuery()->asc(TaskModel::TABLE.'.date_started')->asc(TaskModel::TABLE.'.date_creation');
        } else {
            $filter->getQuery()->asc('column_position')->asc(TaskModel::TABLE.'.position');
        }

        $this->response->html($this->helper->layout->app('gantt/project', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
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

        $values = $values + array(
            'project_id' => $project['id'],
            'column_id' => $this->column->getFirstColumnId($project['id']),
            'position' => 1
        );

        $values = $this->hook->merge('controller:task:form:default', $values, array('default_values' => $values));
        $values = $this->hook->merge('controller:gantt:task:form:default', $values, array('default_values' => $values));

        $this->response->html($this->template->render('gantt/task_creation', array(
            'project' => $project,
            'errors' => $errors,
            'values' => $values,
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id'], true, false, true),
            'colors_list' => $this->color->getList(),
            'categories_list' => $this->category->getList($project['id']),
            'swimlanes_list' => $this->swimlane->getList($project['id'], false, true),
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
                $this->flash->success(t('Task created successfully.'));
                $this->response->redirect($this->helper->url->to('gantt', 'project', array('project_id' => $project['id'])));
            } else {
                $this->flash->failure(t('Unable to create your task.'));
            }
        }

        $this->task($values, $errors);
    }
}
