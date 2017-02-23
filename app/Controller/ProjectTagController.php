<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Class ProjectTagController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class ProjectTagController extends BaseController
{
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project_tag/index', array(
            'project' => $project,
            'tags'    => $this->tagModel->getAllByProject($project['id']),
            'title'   => t('Project tags management'),
        )));
    }

    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['project_id'] = $project['id'];
        }

        $this->response->html($this->template->render('project_tag/create', array(
            'project' => $project,
            'values'  => $values,
            'errors'  => $errors,
        )));
    }

    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        list($valid, $errors) = $this->tagValidator->validateCreation($values);

        if ($valid) {
            if ($this->tagModel->create($project['id'], $values['name']) > 0) {
                $this->flash->success(t('Tag created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create this tag.'));
            }

            $this->response->redirect($this->helper->url->to('ProjectTagController', 'index', array('project_id' => $project['id'])));
        } else {
            $this->create($values, $errors);
        }
    }

    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        if (empty($values)) {
            $values = $tag;
        }

        $this->response->html($this->template->render('project_tag/edit', array(
            'project' => $project,
            'tag'     => $tag,
            'values'  => $values,
            'errors'  => $errors,
        )));
    }

    public function update()
    {
        $project = $this->getProject();
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);
        $values = $this->request->getValues();
        list($valid, $errors) = $this->tagValidator->validateModification($values);

        if ($tag['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        if ($valid) {
            if ($this->tagModel->update($values['id'], $values['name'])) {
                $this->flash->success(t('Tag updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this tag.'));
            }

            $this->response->redirect($this->helper->url->to('ProjectTagController', 'index', array('project_id' => $project['id'])));
        } else {
            $this->edit($values, $errors);
        }
    }

    public function confirm()
    {
        $project = $this->getProject();
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        $this->response->html($this->template->render('project_tag/remove', array(
            'tag'     => $tag,
            'project' => $project,
        )));
    }

    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $tag_id = $this->request->getIntegerParam('tag_id');
        $tag = $this->tagModel->getById($tag_id);

        if ($tag['project_id'] != $project['id']) {
            throw new AccessForbiddenException();
        }

        if ($this->tagModel->remove($tag_id)) {
            $this->flash->success(t('Tag removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this tag.'));
        }

        $this->response->redirect($this->helper->url->to('ProjectTagController', 'index', array('project_id' => $project['id'])));
    }
}
