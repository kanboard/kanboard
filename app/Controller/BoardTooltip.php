<?php

namespace Kanboard\Controller;

/**
 * Board Tooltip
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class BoardTooltip extends Base
{
    /**
     * Get links on mouseover
     *
     * @access public
     */
    public function tasklinks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tooltip_tasklinks', array(
            'links' => $this->taskLink->getAllGroupedByLabel($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Get links on mouseover
     *
     * @access public
     */
    public function externallinks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tooltip_external_links', array(
            'links' => $this->taskExternalLink->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Get subtasks on mouseover
     *
     * @access public
     */
    public function subtasks()
    {
        $task = $this->getTask();
        $this->response->html($this->template->render('board/tooltip_subtasks', array(
            'subtasks' => $this->subtask->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display all attachments during the task mouseover
     *
     * @access public
     */
    public function attachments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_files', array(
            'files' => $this->taskFile->getAll($task['id']),
            'task' => $task,
        )));
    }

    /**
     * Display comments during a task mouseover
     *
     * @access public
     */
    public function comments()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_comments', array(
            'task' => $task,
            'comments' => $this->comment->getAll($task['id'], $this->userSession->getCommentSorting())
        )));
    }

    /**
     * Display task description
     *
     * @access public
     */
    public function description()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('board/tooltip_description', array(
            'task' => $task
        )));
    }

    /**
     * Get recurrence information on mouseover
     *
     * @access public
     */
    public function recurrence()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task_recurrence/info', array(
            'task' => $task,
            'recurrence_trigger_list' => $this->task->getRecurrenceTriggerList(),
            'recurrence_timeframe_list' => $this->task->getRecurrenceTimeframeList(),
            'recurrence_basedate_list' => $this->task->getRecurrenceBasedateList(),
        )));
    }

    /**
     * Display swimlane description in tooltip
     *
     * @access public
     */
    public function swimlane()
    {
        $this->getProject();
        $swimlane = $this->swimlane->getById($this->request->getIntegerParam('swimlane_id'));
        $this->response->html($this->template->render('board/tooltip_description', array('task' => $swimlane)));
    }
}
