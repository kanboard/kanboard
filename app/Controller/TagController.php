<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Class TagController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class TagController extends BaseController
{
    public function index()
    {
        $this->response->html($this->helper->layout->config('tag/index', array(
            'tags' => $this->tagModel->getAllByProject(0),
            'colors'  => $this->colorModel->getList(),
            'title' => t('Settings').' &gt; '.t('Global tags management'),
        )));
    }

    public function create(array $values = array(), array $errors = array())
    {
        if (empty($values)) {
            $values['project_id'] = 0;
        }

        $this->response->html($this->template->render('tag/create', array(
            'values' => $values,
            'colors'  => $this->colorModel->getList(),
            'errors' => $errors,
        )));
    }

    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->tagValidator->validateCreation($values);

        if ($valid) {
            if ($this->tagModel->create(0, $values['name'], $values['color_id']) > 0) {
                $this->flash->success(t('Tag created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create this tag.'));
            }

            $this->response->redirect($this->helper->url->to('TagController', 'index'));
        } else {
            $this->create($values, $errors);
        }
    }

    public function edit(array $values = array(), array $errors = array())
    {
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        if (empty($values)) {
            $values = $tag;
        }

        $this->response->html($this->template->render('tag/edit', array(
            'tag' => $tag,
            'values' => $values,
            'colors'  => $this->colorModel->getList(),
            'errors' => $errors,
        )));
    }

    public function update()
    {
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);
        $values = $this->request->getValues();
        list($valid, $errors) = $this->tagValidator->validateModification($values);

        if ($tag['project_id'] != 0) {
            throw new AccessForbiddenException();
        }

        if ($valid) {
            if ($this->tagModel->update($values['id'], $values['name'], $values['color_id'])) {
                $this->flash->success(t('Tag updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this tag.'));
            }

            $this->response->redirect($this->helper->url->to('TagController', 'index'));
        } else {
            $this->edit($values, $errors);
        }
    }

    public function confirm()
    {
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        $this->response->html($this->template->render('tag/remove', array(
            'tag' => $tag,
        )));
    }

    public function remove()
    {
        $this->checkCSRFParam();
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        if ($tag['project_id'] != 0) {
            throw new AccessForbiddenException();
        }

        if ($this->tagModel->remove($tag_id)) {
            $this->flash->success(t('Tag removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this tag.'));
        }

        $this->response->redirect($this->helper->url->to('TagController', 'index'));
    }
}
