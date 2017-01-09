<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Link Controller
 *
 * @package Kanboard\Controller
 * @author  Olivier Maridat
 * @author  Frederic Guillot
 */
class LinkController extends BaseController
{
    /**
     * Get the current link
     *
     * @access protected
     * @return array
     * @throws PageNotFoundException
     */
    protected function getLink()
    {
        $link = $this->linkModel->getById($this->request->getIntegerParam('link_id'));

        if (empty($link)) {
            throw new PageNotFoundException();
        }

        return $link;
    }

    /**
     * List of labels
     *
     * @access public
     */
    public function show()
    {
        $this->response->html($this->helper->layout->config('link/show', array(
            'links' => $this->linkModel->getMergedList(),
            'title' => t('Settings').' &gt; '.t('Link labels'),
        )));
    }

    /**
     * Add new link label
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function create(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('link/create', array(
            'links'  => $this->linkModel->getMergedList(),
            'values' => $values,
            'errors' => $errors,
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
        list($valid, $errors) = $this->linkValidator->validateCreation($values);

        if ($valid) {
            if ($this->linkModel->create($values['label'], $values['opposite_label']) !== false) {
                $this->flash->success(t('Link added successfully.'));
                $this->response->redirect($this->helper->url->to('LinkController', 'show'), true);
                return;
            } else {
                $this->flash->failure(t('Unable to create your link.'));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Edit form
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $link = $this->getLink();
        $link['label'] = t($link['label']);

        $this->response->html($this->template->render('link/edit', array(
            'values' => $values ?: $link,
            'errors' => $errors,
            'labels' => $this->linkModel->getList($link['id']),
            'link'   => $link,
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
        list($valid, $errors) = $this->linkValidator->validateModification($values);

        if ($valid) {
            if ($this->linkModel->update($values)) {
                $this->flash->success(t('Link updated successfully.'));
                $this->response->redirect($this->helper->url->to('LinkController', 'show'), true);
                return;
            } else {
                $this->flash->failure(t('Unable to update your link.'));
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

        $this->response->html($this->template->render('link/remove', array(
            'link' => $link,
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

        if ($this->linkModel->remove($link['id'])) {
            $this->flash->success(t('Link removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this link.'));
        }

        $this->response->redirect($this->helper->url->to('LinkController', 'show'), true);
    }
}
