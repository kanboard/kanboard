<?php

namespace Controller;

/**
 * Task Status controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Taskstatus extends Base
{
    /**
     * Close a task
     *
     * @access public
     */
    public function close()
    {
        $task = $this->getTask();
        $redirect = $this->request->getStringParam('redirect');

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();

            if ($this->taskStatus->close($task['id'])) {
                $this->session->flash(t('Task closed successfully.'));
            } else {
                $this->session->flashError(t('Unable to close this task.'));
            }

            if ($redirect === 'board') {
                $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
        }

        if ($this->request->isAjax()) {
            $this->response->html($this->template->render('task_status/close', array(
                'task' => $task,
                'redirect' => $redirect,
            )));
        }

        $this->response->html($this->taskLayout('task_status/close', array(
            'task' => $task,
            'redirect' => $redirect,
        )));
    }

    /**
     * Open a task
     *
     * @access public
     */
    public function open()
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {

            $this->checkCSRFParam();

            if ($this->taskStatus->open($task['id'])) {
                $this->session->flash(t('Task opened successfully.'));
            } else {
                $this->session->flashError(t('Unable to open this task.'));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('project_id' => $task['project_id'], 'task_id' => $task['id'])));
        }

        $this->response->html($this->taskLayout('task_status/open', array(
            'task' => $task,
        )));
    }
}
