<?php

namespace Kanboard\Controller;

/**
 * Class CommentMailController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class CommentMailController extends BaseController
{
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $task = $this->getTask();

        $this->response->html($this->helper->layout->task('comment_mail/create', array(
            'values'  => $values,
            'errors'  => $errors,
            'task'    => $task,
            'project' => $project,
            'members' => $this->projectPermissionModel->getMembersWithEmail($project['id']),
        )));
    }

    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();
        $values['task_id'] = $task['id'];
        $values['user_id'] = $this->userSession->getId();

        list($valid, $errors) = $this->commentValidator->validateEmailCreation($values);

        if ($valid) {
            $this->sendByEmail($values);
            $values = $this->prepareComment($values);

            if ($this->commentModel->create($values) !== false) {
                $this->flash->success(t('Comment sent by email successfully.'));
            } else {
                $this->flash->failure(t('Unable to create your comment.'));
            }

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), 'comments'), true);
        } else {
            $this->create($values, $errors);
        }
    }

    protected function sendByEmail(array $values)
    {
        $html = $this->template->render('comment_mail/email', array(
            'email' => $values,
        ));

        $this->emailClient->send(
            $values['email'],
            $values['email'],
            $values['subject'],
            $html
        );
    }

    protected function prepareComment(array $values)
    {
        $values['comment'] .= "\n\n_".t('Sent by email to [%s](mailto:%s) (%s)', $values['email'], $values['email'], $values['subject']).'_';

        unset($values['subject']);
        unset($values['email']);

        return $values;
    }
}
