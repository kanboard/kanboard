<?php

namespace Controller;

/**
 * Link controller
 *
 * @package controller
 * @author  Olivier Maridat
 * @author  Frederic Guillot
 */
class Link extends Base
{
    /**
     * Common layout for config views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());
        $params['config_content_for_layout'] = $this->template->render($template, $params);

        return $this->template->layout('config/layout', $params);
    }

    /**
     * Get the current link
     *
     * @access private
     * @return array
     */
    private function getLink()
    {
        $link = $this->link->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            $this->notfound();
        }

        return $link;
    }

    /**
     * List of links
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $this->response->html($this->layout('link/index', array(
            'links' => $this->link->getMergedList(),
            'values' => $values,
            'errors' => $errors,
            'title' => t('Settings').' &gt; '.t('Task\'s links'),
        )));
    }

    /**
     * Validate and save a new link
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateCreation($values);

        if ($valid) {

            if ($this->link->create($values['label'], $values['opposite_label'])) {
                $this->session->flash(t('Link added successfully.'));
                $this->response->redirect($this->helper->url('link', 'index'));
            }
            else {
                $this->session->flashError(t('Unable to create your link.'));
            }
        }

        $this->index($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $link = $this->getLink();
        $link['label'] = t($link['label']);

        $this->response->html($this->layout('link/edit', array(
            'values' => $values ?: $link,
            'errors' => $errors,
            'labels' => $this->link->getList($link['id']),
            'link' => $link,
            'title' => t('Link modification')
        )));
    }

    /**
     * Edit a link (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateModification($values);

        if ($valid) {
            if ($this->link->update($values)) {
                $this->session->flash(t('Link updated successfully.'));
                $this->response->redirect($this->helper->url('link', 'index'));
            }
            else {
                $this->session->flashError(t('Unable to update your link.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a link
     *
     * @access public
     */
    public function confirm()
    {
        $link = $this->getLink();

        $this->response->html($this->layout('link/remove', array(
            'link' => $link,
            'title' => t('Remove a link')
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
        $link = $this->getLink();

        if ($this->link->remove($link['id'])) {
            $this->session->flash(t('Link removed successfully.'));
        }
        else {
            $this->session->flashError(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url('link', 'index'));
    }
}
