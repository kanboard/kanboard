<?php

namespace Kanboard\Controller;

/**
 * Group Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Group extends Base
{
    /**
     * List all groups
     *
     * @access public
     */
    public function index()
    {
        $paginator = $this->paginator
            ->setUrl('group', 'index')
            ->setMax(30)
            ->setOrder('name')
            ->setQuery($this->group->getQuery())
            ->calculate();

        $this->response->html($this->template->layout('group/index', array(
            'board_selector' => $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId()),
            'title' => t('Groups').' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
        )));
    }

    /**
     * List all users
     *
     * @access public
     */
    public function users()
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->group->getById($group_id);

        $paginator = $this->paginator
            ->setUrl('group', 'users', array('group_id' => $group_id))
            ->setMax(30)
            ->setOrder('username')
            ->setQuery($this->groupMember->getQuery($group_id))
            ->calculate();

        $this->response->html($this->template->layout('group/users', array(
            'board_selector' => $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId()),
            'title' => t('Members of %s', $group['name']).' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
            'group' => $group,
        )));
    }

    /**
     * Display a form to create a new group
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->layout('group/create', array(
            'board_selector' => $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId()),
            'errors' => $errors,
            'values' => $values,
            'title' => t('New group')
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
            if ($this->group->create($values['name']) !== false) {
                $this->flash->success(t('Group created successfully.'));
                $this->response->redirect($this->helper->url->to('group', 'index'));
            } else {
                $this->flash->failure(t('Unable to create your group.'));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Display a form to update a group
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        if (empty($values)) {
            $values = $this->group->getById($this->request->getIntegerParam('group_id'));
        }

        $this->response->html($this->template->layout('group/edit', array(
            'board_selector' => $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId()),
            'errors' => $errors,
            'values' => $values,
            'title' => t('Edit group')
        )));
    }

    /**
     * Validate and save a group
     *
     * @access public
     */
    public function update()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->groupValidator->validateModification($values);

        if ($valid) {
            if ($this->group->update($values) !== false) {
                $this->flash->success(t('Group updated successfully.'));
                $this->response->redirect($this->helper->url->to('group', 'index'));
            } else {
                $this->flash->failure(t('Unable to update your group.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Form to associate a user to a group
     *
     * @access public
     */
    public function associate(array $values = array(), array $errors = array())
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->group->getbyId($group_id);

        if (empty($values)) {
            $values['group_id'] = $group_id;
        }

        $this->response->html($this->template->layout('group/associate', array(
            'board_selector' => $this->projectUserRole->getActiveProjectsByUser($this->userSession->getId()),
            'users' => $this->user->prepareList($this->groupMember->getNotMembers($group_id)),
            'group' => $group,
            'errors' => $errors,
            'values' => $values,
            'title' => t('Add group member to "%s"', $group['name']),
        )));
    }

    /**
     * Add user to a group
     *
     * @access public
     */
    public function addUser()
    {
        $values = $this->request->getValues();

        if (isset($values['group_id']) && isset($values['user_id'])) {
            if ($this->groupMember->addUser($values['group_id'], $values['user_id'])) {
                $this->flash->success(t('Group member added successfully.'));
                $this->response->redirect($this->helper->url->to('group', 'users', array('group_id' => $values['group_id'])));
            } else {
                $this->flash->failure(t('Unable to add group member.'));
            }
        }

        $this->associate($values);
    }

    /**
     * Confirmation dialog to remove a user from a group
     *
     * @access public
     */
    public function dissociate()
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $user_id = $this->request->getIntegerParam('user_id');
        $group = $this->group->getById($group_id);
        $user = $this->user->getById($user_id);

        $this->response->html($this->template->layout('group/dissociate', array(
            'group' => $group,
            'user' => $user,
            'title' => t('Remove user from group "%s"', $group['name']),
        )));
    }

    /**
     * Remove a user from a group
     *
     * @access public
     */
    public function removeUser()
    {
        $this->checkCSRFParam();
        $group_id = $this->request->getIntegerParam('group_id');
        $user_id = $this->request->getIntegerParam('user_id');

        if ($this->groupMember->removeUser($group_id, $user_id)) {
            $this->flash->success(t('User removed successfully from this group.'));
        } else {
            $this->flash->failure(t('Unable to remove this user from the group.'));
        }

        $this->response->redirect($this->helper->url->to('group', 'users', array('group_id' => $group_id)));
    }

    /**
     * Confirmation dialog to remove a group
     *
     * @access public
     */
    public function confirm()
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->group->getById($group_id);

        $this->response->html($this->template->layout('group/remove', array(
            'group' => $group,
            'title' => t('Remove group'),
        )));
    }

    /**
     * Remove a group
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $group_id = $this->request->getIntegerParam('group_id');

        if ($this->group->remove($group_id)) {
            $this->flash->success(t('Group removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this group.'));
        }

        $this->response->redirect($this->helper->url->to('group', 'index'));
    }
}
