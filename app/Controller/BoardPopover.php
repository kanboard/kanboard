<?php

namespace Kanboard\Controller;

/**
 * Board Popover
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class BoardPopover extends Base
{
    /**
     * Change a task assignee directly from the board
     *
     * @access public
     */
    public function changeAssignee()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/popover_assignee', array(
            'values' => $task,
            'users_list' => $this->projectUserRole->getAssignableUsersList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate an assignee modification
     *
     * @access public
     */
    public function updateAssignee()
    {
        $values = $this->request->getValues();

        list($valid, ) = $this->taskValidator->validateAssigneeModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->flash->success(t('Task updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Change a task category directly from the board
     *
     * @access public
     */
    public function changeCategory()
    {
        $task = $this->getTask();
        $project = $this->project->getById($task['project_id']);

        $this->response->html($this->template->render('board/popover_category', array(
            'values' => $task,
            'categories_list' => $this->category->getList($project['id']),
            'project' => $project,
        )));
    }

    /**
     * Validate a category modification
     *
     * @access public
     */
    public function updateCategory()
    {
        $values = $this->request->getValues();

        list($valid, ) = $this->taskValidator->validateCategoryModification($values);

        if ($valid && $this->taskModification->update($values)) {
            $this->flash->success(t('Task updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update your task.'));
        }

        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $values['project_id'])));
    }

    /**
     * Screenshot popover
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('file/screenshot', array(
            'task' => $task,
            'redirect' => 'board',
        )));
    }

    /**
     * Confirmation before to close all column tasks
     *
     * @access public
     */
    public function confirmCloseColumnTasks()
    {
        $project = $this->getProject();
        $column_id = $this->request->getIntegerParam('column_id');
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        $this->response->html($this->template->render('board/popover_close_all_tasks_column', array(
            'project' => $project,
            'nb_tasks' => $this->taskFinder->countByColumnAndSwimlaneId($project['id'], $column_id, $swimlane_id),
            'column' => $this->board->getColumnTitleById($column_id),
            'swimlane' => $this->swimlane->getNameById($swimlane_id) ?: t($project['default_swimlane']),
            'values' => array('column_id' => $column_id, 'swimlane_id' => $swimlane_id),
        )));
    }

    /**
     * Close all column tasks
     *
     * @access public
     */
    public function closeColumnTasks()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        $this->taskStatus->closeTasksBySwimlaneAndColumn($values['swimlane_id'], $values['column_id']);
        $this->flash->success(t('All tasks of the column "%s" and the swimlane "%s" have been closed successfully.', $this->board->getColumnTitleById($values['column_id']), $this->swimlane->getNameById($values['swimlane_id']) ?: t($project['default_swimlane'])));
        $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $project['id'])));
    }
}
