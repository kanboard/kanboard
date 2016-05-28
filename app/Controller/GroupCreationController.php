<?php

namespace Kanboard\Controller;

/**
 * Class GroupCreationController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class GroupCreationController extends BaseController
{
    /**
     * Display a form to create a new group
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('group_creation/show', array(
            'errors' => $errors,
            'values' => $values,
        )));
    }

    /**
     * Validate and save a new group
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->groupValidator->validateCreation($values);

        if ($valid) {
            if ($this->groupModel->create($values['name']) !== false) {
                $this->flash->success(t('Group created successfully.'));
                return $this->response->redirect($this->helper->url->to('GroupListController', 'index'), true);
            } else {
                $this->flash->failure(t('Unable to create your group.'));
            }
        }

        return $this->show($values, $errors);
    }
}
