<?php

namespace Kanboard\Controller;

use Kanboard\Model\UserMetadataModel;

/**
 * Class UserModificationController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserModificationController extends BaseController
{
    /**
     * Display a form to edit user information
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function show(array $values = array(), array $errors = array())
    {
        $user = $this->getUser();

        if (empty($values)) {
            $values = $user;
            unset($values['password']);
            $values[UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS] = $this->userMetadataModel->get($user['id'], UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS, 0);
        }

        return $this->response->html($this->helper->layout->user('user_modification/show', array(
            'values' => $values,
            'errors' => $errors,
            'user' => $user,
            'themes' => $this->themeModel->getThemes(),
            'timezones' => $this->timezoneModel->getTimezones(true),
            'languages' => $this->languageModel->getLanguages(true),
            'roles' => $this->role->getApplicationRoles(),
        )));
    }

    /**
     * Save user information
     */
    public function save()
    {
        $user = $this->getUser();
        $values = $this->request->getValues();
        $taskSearchAllFieldsFormKey = str_replace('.', '_', UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS);
        $taskSearchAllFields = isset($values[$taskSearchAllFieldsFormKey]) ? $values[$taskSearchAllFieldsFormKey] : 0;
        unset($values[UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS]);
        unset($values[$taskSearchAllFieldsFormKey]);

        if (! $this->userSession->isAdmin()) {
            $values = array(
                'id' => $this->userSession->getId(),
                'username' => isset($values['username']) ? $values['username'] : '',
                'name' => isset($values['name']) ? $values['name'] : '',
                'email' => isset($values['email']) ? $values['email'] : '',
                'theme' => isset($values['theme']) ? $values['theme'] : '',
                'timezone' => isset($values['timezone']) ? $values['timezone'] : '',
                'language' => isset($values['language']) ? $values['language'] : '',
                'filter' => isset($values['filter']) ? $values['filter'] : '',
            );
        }

        list($valid, $errors) = $this->userValidator->validateModification($values);

        if ($valid) {
            if ($this->userModel->update($values) && $this->userMetadataModel->save($user['id'], array(
                UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS => $taskSearchAllFields,
            ))) {
                $this->flash->success(t('User updated successfully.'));
                $this->response->redirect($this->helper->url->to('UserViewController', 'show', array('user_id' => $user['id'])), true);
                return;
            } else {
                $this->flash->failure(t('Unable to update this user.'));
            }
        }

        $values[UserMetadataModel::KEY_TASK_SEARCH_ALL_FIELDS] = $taskSearchAllFields;
        $this->show($values, $errors);
    }
}
