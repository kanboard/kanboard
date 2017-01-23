<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Core\Security\Role;
use Kanboard\Notification\MailNotification;

/**
 * Class UserInviteController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserInviteController extends BaseController
{
    public function show(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('user_invite/show', array(
            'projects' => $this->projectModel->getList(),
            'errors'   => $errors,
            'values'   => $values,
        )));
    }

    public function save()
    {
        $values = $this->request->getValues();

        if (! empty($values['emails']) && isset($values['project_id'])) {
            $emails = explode("\r\n", trim($values['emails']));
            $nb = $this->inviteModel->createInvites($emails, $values['project_id']);
            $this->flash->success($nb > 1 ? t('%d invitations were sent.', $nb) : t('%d invitation was sent.', $nb));
        }

        $this->response->redirect($this->helper->url->to('UserListController', 'show'));
    }

    public function signup(array $values = array(), array $errors = array())
    {
        $invite = $this->getInvite();

        $this->response->html($this->helper->layout->app('user_invite/signup', array(
            'no_layout'    => true,
            'not_editable' => true,
            'token'        => $invite['token'],
            'errors'       => $errors,
            'values'       => $values + array('email' => $invite['email']),
            'timezones'    => $this->timezoneModel->getTimezones(true),
            'languages'    => $this->languageModel->getLanguages(true),
        )));
    }

    public function register()
    {
        $invite = $this->getInvite();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->userValidator->validateCreation($values);

        if ($valid) {
            $this->createUser($invite, $values);
        } else {
            $this->signup($values, $errors);
        }
    }

    protected function getInvite()
    {
        $token = $this->request->getStringParam('token');

        if (empty($token)) {
            throw PageNotFoundException::getInstance()->withoutLayout();
        }

        $invite = $this->inviteModel->getByToken($token);

        if (empty($invite)) {
            throw PageNotFoundException::getInstance()->withoutLayout();
        }

        return $invite;
    }

    protected function createUser(array $invite, array $values)
    {
        $user_id = $this->userModel->create($values);

        if ($user_id !== false) {
            if ($invite['project_id'] != 0) {
                $this->projectUserRoleModel->addUser($invite['project_id'], $user_id, Role::PROJECT_MEMBER);
            }

            if (! empty($values['notifications_enabled'])) {
                $this->userNotificationTypeModel->saveSelectedTypes($user_id, array(MailNotification::TYPE));
            }

            $this->inviteModel->remove($invite['email']);

            $this->flash->success(t('User created successfully.'));
            $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        } else {
            $this->flash->failure(t('Unable to create this user.'));
            $this->response->redirect($this->helper->url->to('UserInviteController', 'signup'));
        }
    }
}
