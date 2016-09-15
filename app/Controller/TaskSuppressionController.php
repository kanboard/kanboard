<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Class TaskSuppressionController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TaskSuppressionController extends BaseController
{
    /**
     * Confirmation dialog box before to remove the task
     */
    public function confirm()
    {
        $task = $this->getTask();

        if (! $this->helper->projectRole->canRemoveTask($task)) {
            throw new AccessForbiddenException();
        }

        $this->response->html($this->template->render('task_suppression/remove', array(
            'task' => $task,
            'redirect' => $this->request->getStringParam('redirect'),
        )));
    }

    /**
     * Remove a task
     */
    public function remove()
    {
        $task = $this->getTask();
        $this->checkCSRFParam();

        if (! $this->helper->projectRole->canRemoveTask($task)) {
            throw new AccessForbiddenException();
        }

        if ($this->taskModel->remove($task['id'])) {
            $this->flash->success(t('Task removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this task.'));
        }

        $redirect = $this->request->getStringParam('redirect') === '';
        $this->response->redirect($this->helper->url->to('BoardViewController', 'show', array('project_id' => $task['project_id'])), $redirect);
    }
}
