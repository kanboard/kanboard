<?php

namespace Kanboard\Controller;

/**
 * Task Status controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskStatusController extends BaseController
{
    /**
     * Close a task
     *
     * @access public
     */
    public function close()
    {
        $this->changeStatus('close', 'task_status/close', t('Task closed successfully.'), t('Unable to close this task.'));
    }

    /**
     * Open a task
     *
     * @access public
     */
    public function open()
    {
        $this->changeStatus('open', 'task_status/open', t('Task opened successfully.'), t('Unable to open this task.'));
    }

    /**
     * Common method to change status
     *
     * @access private
     * @param  string $method
     * @param  string $template
     * @param  string $success_message
     * @param  string $failure_message
     */
    private function changeStatus($method, $template, $success_message, $failure_message)
    {
        $task = $this->getTask();

        if ($this->request->getStringParam('confirmation') === 'yes') {
            $this->checkCSRFParam();

            if ($this->taskStatusModel->$method($task['id'])) {
                $this->flash->success($success_message);
            } else {
                $this->flash->failure($failure_message);
            }

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])), true);
        } else {
            $this->response->html($this->template->render($template, array(
                'task' => $task,
            )));
        }
    }
}
