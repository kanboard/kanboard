<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Controller\PageNotFoundException;

/**
 * TaskInternalLink Controller
 *
 * @package  Kanboard\Controller
 * @author   Olivier Maridat
 * @author   Frederic Guillot
 */
class TaskInternalLinkController extends BaseController
{
    /**
     * Creation form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws PageNotFoundException
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();

        if (empty($values)) {
          $values['another_tasklink'] = $this->request->getIntegerParam('another_tasklink', 0);
          $values = $this->hook->merge('controller:tasklink:form:default', $values, array('default_values' => $values));
        }

        $this->response->html($this->template->render('task_internal_link/create', array(
            'values' => $values,
            'errors' => $errors,
            'task' => $task,
            'labels' => $this->linkModel->getList(0, false),
        )));
    }

    /**
     * Validation and creation
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();
        $values = $this->request->getValues();
        $values['task_id'] = $task['id'];

        list($valid, $errors) = $this->taskLinkValidator->validateCreation($values);

        if ($valid) {
            $opposite_task = $this->taskFinderModel->getById($values['opposite_task_id']);

            if (! $this->projectPermissionModel->isUserAllowed($opposite_task['project_id'], $this->userSession->getId())) {
                throw new AccessForbiddenException();
            }

            if ($this->taskLinkModel->create($values['task_id'], $values['opposite_task_id'], $values['link_id']) !== false) {
                $this->flash->success(t('Link added successfully.'));

                if (isset($values['another_tasklink']) && $values['another_tasklink'] == 1) {
                    return $this->create(array(
                        'project_id' => $task['project_id'],
                        'task_id' => $task['id'],
                        'link_id' => $values['link_id'],
                        'another_tasklink' => 1
                    ));
                }

                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])), true);
            }

            $errors = array('title' => array(t('The exact same link already exists')));
            $this->flash->failure(t('Unable to create your link.'));
        }

        return $this->create($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     * @param array $values
     * @param array $errors
     * @throws PageNotFoundException
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $task = $this->getTask();
        $task_link = $this->getInternalTaskLink($task);

        if (empty($values)) {
            $opposite_task = $this->taskFinderModel->getById($task_link['opposite_task_id']);
            $values = $task_link;
            $values['title'] = '#'.$opposite_task['id'].' - '.$opposite_task['title'];
        }

        $this->response->html($this->template->render('task_internal_link/edit', array(
            'values' => $values,
            'errors' => $errors,
            'task_link' => $task_link,
            'task' => $task,
            'labels' => $this->linkModel->getList(0, false)
        )));
    }

    /**
     * Validation and update
     *
     * @access public
     */
    public function update()
    {
        $task = $this->getTask();
        $task_link = $this->getInternalTaskLink($task);

        $values = $this->request->getValues();
        $values['task_id'] = $task['id'];
        $values['id'] = $task_link['id'];

        list($valid, $errors) = $this->taskLinkValidator->validateModification($values);

        if ($valid) {
            $opposite_task = $this->taskFinderModel->getById($values['opposite_task_id']);

            if (! $this->projectPermissionModel->isUserAllowed($opposite_task['project_id'], $this->userSession->getId())) {
                throw new AccessForbiddenException();
            }

            if ($this->taskLinkModel->update($values['id'], $values['task_id'], $values['opposite_task_id'], $values['link_id'])) {
                $this->flash->success(t('Link updated successfully.'));
                return $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])).'#links');
            }

            $this->flash->failure(t('Unable to update your link.'));
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a link
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $link = $this->getInternalTaskLink($task);

        $this->response->html($this->template->render('task_internal_link/remove', array(
            'link' => $link,
            'task' => $task,
        )));
    }

    /**
     * Remove a link
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();
        $link = $this->getInternalTaskLink($task);

        if ($this->taskLinkModel->remove($link['id'])) {
            $this->flash->success(t('Link removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])));
    }
}
