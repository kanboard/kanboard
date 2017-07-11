<?php

namespace Kanboard\Controller;

use Kanboard\Model\GroupModel;
use Kanboard\Model\UserModel;

/**
 * Group Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class GroupListController extends BaseController
{
    /**
     * List all groups
     *
     * @access public
     */
    public function index()
    {
        $paginator = $this->paginator
            ->setUrl('GroupListController', 'index')
            ->setMax(30)
            ->setOrder(GroupModel::TABLE.'.name')
            ->setQuery($this->groupModel->getQuery())
            ->calculate();

        $this->response->html($this->helper->layout->app('group/index', array(
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
        $group = $this->groupModel->getById($group_id);

        $paginator = $this->paginator
            ->setUrl('GroupListController', 'users', array('group_id' => $group_id))
            ->setMax(30)
            ->setOrder(UserModel::TABLE.'.username')
            ->setQuery($this->groupMemberModel->getQuery($group_id))
            ->calculate();

        $this->response->html($this->helper->layout->app('group/users', array(
            'title' => t('Members of %s', $group['name']).' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
            'group' => $group,
        )));
    }

    /**
     * Form to associate a user to a group
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function associate(array $values = array(), array $errors = array())
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->groupModel->getById($group_id);

        if (empty($values)) {
            $values['group_id'] = $group_id;
        }

        $this->response->html($this->template->render('group/associate', array(
            'users' => $this->userModel->prepareList($this->groupMemberModel->getNotMembers($group_id)),
            'group' => $group,
            'errors' => $errors,
            'values' => $values,
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
            if ($this->groupMemberModel->addUser($values['group_id'], $values['user_id'])) {
                $this->flash->success(t('Group member added successfully.'));
                return $this->response->redirect($this->helper->url->to('GroupListController', 'users', array('group_id' => $values['group_id'])), true);
            } else {
                $this->flash->failure(t('Unable to add group member.'));
            }
        }

        return $this->associate($values);
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
        $group = $this->groupModel->getById($group_id);
        $user = $this->userModel->getById($user_id);

        $this->response->html($this->template->render('group/dissociate', array(
            'group' => $group,
            'user' => $user,
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

        if ($this->groupMemberModel->removeUser($group_id, $user_id)) {
            $this->flash->success(t('User removed successfully from this group.'));
        } else {
            $this->flash->failure(t('Unable to remove this user from the group.'));
        }

        $this->response->redirect($this->helper->url->to('GroupListController', 'users', array('group_id' => $group_id)), true);
    }

    /**
     * Confirmation dialog to remove a group
     *
     * @access public
     */
    public function confirm()
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->groupModel->getById($group_id);

        $this->response->html($this->template->render('group/remove', array(
            'group' => $group,
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

        if ($this->groupModel->remove($group_id)) {
            $this->flash->success(t('Group removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this group.'));
        }

        $this->response->redirect($this->helper->url->to('GroupListController', 'index'), true);
    }
}
