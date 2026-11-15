<?php

namespace Kanboard\Controller;

/**
 * Task Duplication controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskDuplicationController extends BaseController
{
    /**
     * Duplicate a task
     *
     * @access public
     */
    public function duplicate()
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {
            $this->checkCSRFParam();
            $task_id = $this->taskDuplicationModel->duplicate($task['id']);

            if ($task_id > 0) {
                $this->flash->success(t('Task created successfully.'));
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)));
            } else {
                $this->flash->failure(t('Unable to create this task.'));
                return $this->response->redirect($this->helper->url->to('TaskDuplicationController', 'duplicate', array('task_id' => $task['id'])), true);
            }
        }

        return $this->response->html($this->template->render('task_duplication/duplicate', array(
            'task' => $task,
        )));
    }

    /**
     * Move a task to another project
     *
     * @access public
     */
    public function move()
    {
        $task = $this->getTask();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            list($valid, ) = $this->taskValidator->validateProjectModification($values);

            if ($valid) {
                $this->checkDestinationTaskCreation($values['project_id'], $values['column_id'] ?? $task['column_id']);

                if ($this->taskProjectMoveModel->moveToProject(
                    $task['id'],
                    $values['project_id'],
                    $values['swimlane_id'],
                    $values['column_id'],
                    $values['category_id'],
                    $values['owner_id']
                )) {
                    $this->flash->success(t('Task updated successfully.'));
                    return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])));
                }
            }

            $this->flash->failure(t('Unable to update your task.'));
        }

        return $this->chooseDestination($task, 'task_duplication/move');
    }

    /**
     * Duplicate a task to another project
     *
     * @access public
     */
    public function copy()
    {
        $task = $this->getTask();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            list($valid, ) = $this->taskValidator->validateProjectModification($values);

            if ($valid) {
                $this->checkDestinationTaskCreation($values['project_id'], $values['column_id'] ?? $task['column_id']);

                $task_id = $this->taskProjectDuplicationModel->duplicateToProject(
                    $task['id'],
                    $values['project_id'],
                    $values['swimlane_id'],
                    $values['column_id'],
                    $values['category_id'],
                    $values['owner_id']
                );

                if ($task_id > 0) {
                    $this->flash->success(t('Task created successfully.'));
                    return $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $task['project_id'])), true);
                }
            }

            $this->flash->failure(t('Unable to create your task.'));
        }

        return $this->chooseDestination($task, 'task_duplication/copy');
    }

    /**
     * Choose destination when move/copy task to another project
     *
     * @access private
     * @param  array   $task
     * @param  string  $template
     */
    private function chooseDestination(array $task, $template)
    {
        $values = array();
        $projects_list = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());

        unset($projects_list[$task['project_id']]);

        // Offer only the projects and columns that move() and copy() accept
        $projects_list = array_filter($projects_list, function ($project_id) {
            return $this->hasColumnForTaskCreation($project_id);
        }, ARRAY_FILTER_USE_KEY);

        if (! empty($projects_list)) {
            $dst_project_id = $this->request->getIntegerParam('dst_project_id', key($projects_list));

            if (! isset($projects_list[$dst_project_id])) {
                $dst_project_id = key($projects_list);
            }

            $swimlanes_list = $this->swimlaneModel->getList($dst_project_id, false, true);
            $columns_list = $this->getColumnsForTaskCreation($dst_project_id);
            $categories_list = $this->categoryModel->getList($dst_project_id);
            $users_list = $this->projectUserRoleModel->getAssignableUsersList($dst_project_id);

            $values = $this->taskDuplicationModel->checkDestinationProjectValues($task);
            $values['project_id'] = $dst_project_id;
        } else {
            $swimlanes_list = array();
            $columns_list = array();
            $categories_list = array();
            $users_list = array();
        }

        $this->response->html($this->template->render($template, array(
            'values' => $values,
            'task' => $task,
            'projects_list' => $projects_list,
            'swimlanes_list' => $swimlanes_list,
            'columns_list' => $columns_list,
            'categories_list' => $categories_list,
            'users_list' => $users_list,
        )));
    }

    /**
     * Get the columns of the project where the user can create a task
     *
     * @access private
     * @param  integer $project_id
     * @return array
     */
    private function getColumnsForTaskCreation($project_id)
    {
        return array_filter($this->columnModel->getList($project_id), function ($column_id) use ($project_id) {
            return $this->canCreateTaskInDestination($project_id, $column_id);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return true if the user can create a task in at least one column of the project
     *
     * @access private
     * @param  integer $project_id
     * @return bool
     */
    private function hasColumnForTaskCreation($project_id)
    {
        foreach (array_keys($this->columnModel->getList($project_id)) as $column_id) {
            if ($this->canCreateTaskInDestination($project_id, $column_id)) {
                return true;
            }
        }

        return false;
    }
}
