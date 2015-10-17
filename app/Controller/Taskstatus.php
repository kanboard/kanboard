<?php

namespace Kanboard\Controller;

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
        $this->changeStatus($task, 'close', t('Task closed successfully.'), t('Unable to close this task.'));
        $this->renderTemplate($task, 'task_status/close');
    }

    /**
     * Open a task
     *
     * @access public
     */
    public function open()
    {
        $task = $this->getTask();
        $this->changeStatus($task, 'open', t('Task opened successfully.'), t('Unable to open this task.'));
        $this->renderTemplate($task, 'task_status/open');
    }

    private function changeStatus(array $task, $method, $success_message, $failure_message)
    {
        if ($this->request->getStringParam('confirmation') === 'yes') {
            $this->checkCSRFParam();

            if ($this->taskStatus->$method($task['id'])) {
                $this->session->flash($success_message);
            } else {
                $this->session->flashError($failure_message);
            }

            if ($this->request->getStringParam('redirect') === 'board') {
                $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
        }
    }

    private function renderTemplate(array $task, $template)
    {
        $redirect = $this->request->getStringParam('redirect');

        if ($this->request->isAjax()) {
            $this->response->html($this->template->render($template, array(
                'task' => $task,
                'redirect' => $redirect,
            )));
        }

        $this->response->html($this->taskLayout($template, array(
            'task' => $task,
            'redirect' => $redirect,
        )));
    }
}
