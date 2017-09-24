<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Task Creation Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskCreationController extends BaseController
{
    /**
     * Display a form to create a new task
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws PageNotFoundException
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $swimlanesList = $this->swimlaneModel->getList($project['id'], false, true);
        $values += $this->prepareValues($project['is_private'], $swimlanesList);

        $values = $this->hook->merge('controller:task:form:default', $values, array('default_values' => $values));
        $values = $this->hook->merge('controller:task-creation:form:default', $values, array('default_values' => $values));

        $this->response->html($this->template->render('task_creation/show', array(
            'project' => $project,
            'errors' => $errors,
            'values' => $values + array('project_id' => $project['id']),
            'columns_list' => $this->columnModel->getList($project['id']),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], true, false, $project['is_private'] == 1),
            'categories_list' => $this->categoryModel->getList($project['id']),
            'swimlanes_list' => $swimlanesList,
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
        $values['project_id'] = $project['id'];

        list($valid, $errors) = $this->taskValidator->validateCreation($values);

        if (! $valid) {
            $this->flash->failure(t('Unable to create your task.'));
            $this->show($values, $errors);
        } else if (! $this->helper->projectRole->canCreateTaskInColumn($project['id'], $values['column_id'])) {
            $this->flash->failure(t('You cannot create tasks in this column.'));
            $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
        } else {
            $task_id = $this->taskCreationModel->create($values);

            if ($task_id > 0) {
                $this->flash->success(t('Task created successfully.'));
                $this->afterSave($project, $values, $task_id);
            } else {
                $this->flash->failure(t('Unable to create this task.'));
                $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
            }
        }
    }

    /**
     * Duplicate created tasks to multiple projects
     *
     * @throws PageNotFoundException
     */
    public function duplicateProjects()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        if (isset($values['project_ids'])) {
            foreach ($values['project_ids'] as $project_id) {
                $this->taskProjectDuplicationModel->duplicateToProject($values['task_id'], $project_id);
            }
        }

        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
    }

    /**
     * Executed after the task is saved
     *
     * @param array   $project
     * @param array   $values
     * @param integer $task_id
     */
    protected function afterSave(array $project, array &$values, $task_id)
    {
        if (isset($values['duplicate_multiple_projects']) && $values['duplicate_multiple_projects'] == 1) {
            $this->chooseProjects($project, $task_id);
        } elseif (isset($values['another_task']) && $values['another_task'] == 1) {
            $this->show(array(
                'owner_id' => $values['owner_id'],
                'color_id' => $values['color_id'],
                'category_id' => isset($values['category_id']) ? $values['category_id'] : 0,
                'column_id' => $values['column_id'],
                'swimlane_id' => isset($values['swimlane_id']) ? $values['swimlane_id'] : 0,
                'another_task' => 1,
            ));
        } else {
            $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $project['id'])), true);
        }
    }

    /**
     * Prepare form values
     *
     * @access protected
     * @param  bool  $isPrivateProject
     * @param  array $swimlanesList
     * @return array
     */
    protected function prepareValues($isPrivateProject, array $swimlanesList)
    {
        $values = array(
            'swimlane_id' => $this->request->getIntegerParam('swimlane_id', key($swimlanesList)),
            'column_id'   => $this->request->getIntegerParam('column_id'),
            'color_id'    => $this->colorModel->getDefaultColor(),
        );

        if ($isPrivateProject) {
            $values['owner_id'] = $this->userSession->getId();
        }

        return $values;
    }

    /**
     * Choose projects
     *
     * @param array $project
     * @param integer $task_id
     */
    protected function chooseProjects(array $project, $task_id)
    {
        $task = $this->taskFinderModel->getById($task_id);
        $projects = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        unset($projects[$project['id']]);

        $this->response->html($this->template->render('task_creation/duplicate_projects', array(
            'project' => $project,
            'task' => $task,
            'projects_list' => $projects,
            'values' => array('task_id' => $task['id'])
        )));
    }
}
