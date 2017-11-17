<?php

namespace Kanboard\Controller;

/**
 * Class TaskMailController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TaskMailController extends BaseController
{
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $task = $this->getTask();

        $this->response->html($this->helper->layout->task('task_mail/create', array(
            'values'  => $values,
            'errors'  => $errors,
            'task'    => $task,
            'project' => $project,
            'members' => $this->projectPermissionModel->getMembersWithEmail($project['id']),
        )));
    }

    public function send()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->taskValidator->validateEmailCreation($values);

        if ($valid) {
            $this->sendByEmail($values, $task);
            $this->flash->success(t('Task sent by email successfully.'));

            $this->commentModel->create(array(
                'comment' => t('This task was sent by email to "%s" with subject "%s".', $values['emails'], $values['subject']),
                'user_id' => $this->userSession->getId(),
                'task_id' => $task['id'],
            ));

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'), true);
        } else {
            $this->create($values, $errors);
        }
    }

    protected function sendByEmail(array $values, array $task)
    {
        $emails = explode_csv_field($values['emails']);
        $html = $this->template->render('task_mail/email', array('task' => $task));

        foreach ($emails as $email) {
            $this->emailClient->send(
                $email,
                $email,
                $values['subject'],
                $html
            );
        }
    }
}
