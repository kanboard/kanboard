<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\Role;
use Kanboard\Notification\MailNotification;

/**
 * Class UserCreationController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserCreationController extends BaseController
{
    /**
     * Display a form to create a new user
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('user_creation/show', array(
            'themes' => $this->themeModel->getThemes(),
            'timezones' => $this->timezoneModel->getTimezones(true),
            'languages' => $this->languageModel->getLanguages(true),
            'roles' => $this->role->getApplicationRoles(),
            'projects' => $this->projectModel->getList(),
            'errors' => $errors,
            'values' => $values + array('role' => Role::APP_USER),
        )));
    }

    /**
     * Validate and save a new user
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->userValidator->validateCreation($values);

        if ($valid) {
            $this->createUser($values);
        } else {
            $this->show($values, $errors);
        }
    }

    /**
     * Create user
     *
     * @param array $values
     */
    protected function createUser(array $values)
    {
        $project_id = empty($values['project_id']) ? 0 : $values['project_id'];
        unset($values['project_id']);

        $user_id = $this->userModel->create($values);

        if ($user_id !== false) {
            if ($project_id !== 0) {
                $this->projectUserRoleModel->addUser($project_id, $user_id, Role::PROJECT_MEMBER);
            }

            if (! empty($values['notifications_enabled'])) {
                $this->userNotificationTypeModel->saveSelectedTypes($user_id, array(MailNotification::TYPE));
            }

            $this->flash->success(t('User created successfully.'));
            $this->response->redirect($this->helper->url->to('UserViewController', 'show', array('user_id' => $user_id)));
        } else {
            $this->flash->failure(t('Unable to create your user.'));
            $this->response->redirect($this->helper->url->to('UserListController', 'show'));
        }
    }
}
