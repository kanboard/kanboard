<?php

namespace Kanboard\Controller;

/**
 * Class GroupModificationController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class GroupModificationController extends BaseController
{
    /**
     * Display a form to update a group
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        if (empty($values)) {
            $values = $this->groupModel->getById($this->request->getIntegerParam('group_id'));
        }

        $this->response->html($this->template->render('group_modification/show', array(
            'errors' => $errors,
            'values' => $values,
        )));
    }

    /**
     * Validate and save a group
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->groupValidator->validateModification($values);

        if ($valid) {
            if ($this->groupModel->update($values) !== false) {
                $this->flash->success(t('Group updated successfully.'));
                return $this->response->redirect($this->helper->url->to('GroupListController', 'index'), true);
            } else {
                $this->flash->failure(t('Unable to update your group.'));
            }
        }

        return $this->show($values, $errors);
    }
}
