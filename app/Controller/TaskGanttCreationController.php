<?php

namespace Kanboard\Controller;

/**
 * Class TaskGanttCreationController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TaskGanttCreationController extends BaseController
{
    /**
     * Simplified form to create a new task
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $values = $values + array(
                'project_id' => $project['id'],
                'column_id' => $this->columnModel->getFirstColumnId($project['id']),
                'position' => 1
            );

        $values = $this->hook->merge('controller:task:form:default', $values, array('default_values' => $values));
        $values = $this->hook->merge('controller:gantt:task:form:default', $values, array('default_values' => $values));

        $this->response->html($this->template->render('task_gantt_creation/show', array(
            'project' => $project,
            'errors' => $errors,
            'values' => $values,
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private']),
            'categories_list' => $this->categoryModel->getList($project['id']),
            'swimlanes_list' => $this->swimlaneModel->getList($project['id'], false, true),
            'title' => $project['name'].' &gt; '.t('New task')
        )));
    }

    /**
     * Validate and save a new task
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if ($valid) {
            $task_id = $this->taskCreationModel->create($values);

            if ($task_id !== false) {
                $this->flash->success(t('Task created successfully.'));
                return $this->response->redirect($this->helper->url->to('TaskGanttController', 'show', array('project_id' => $project['id'])));
            } else {
                $this->flash->failure(t('Unable to create your task.'));
            }
        }

        return $this->show($values, $errors);
    }
}
