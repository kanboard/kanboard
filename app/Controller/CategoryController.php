<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;

/**
 * Category Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class CategoryController extends BaseController
{
    /**
     * List of categories for a given project
     *
     * @access public
     * @throws PageNotFoundException
     */
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('category/index', array(
            'categories' => $this->categoryModel->getAll($project['id']),
            'project'    => $project,
            'colors'  => $this->colorModel->getList(),
            'title'      => t('Categories'),
        )));
    }

    /**
     * Show form to create new category
     *
     * @param array $values
     * @param array $errors
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('category/create', array(
            'values'  => $values + array('project_id' => $project['id']),
            'colors'  => $this->colorModel->getList(),
            'errors'  => $errors,
            'project' => $project,
        )));
    }

    /**
     * Validate and save a new category
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $values['project_id'] = $project['id'];

        list($valid, $errors) = $this->categoryValidator->validateCreation($values);

        if ($valid) {
            if ($this->categoryModel->create($values) !== false) {
                $this->flash->success(t('Your category has been created successfully.'));
                $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])), true);
                return;
            } else {
                $errors = array('name' => array(t('Another category with the same name exists in this project')));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Edit a category (display the form)
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws PageNotFoundException
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $category = $this->getCategory($project);

        $this->response->html($this->template->render('category/edit', array(
            'values'  => empty($values) ? $category : $values,
            'colors'  => $this->colorModel->getList(),
            'errors'  => $errors,
            'project' => $project,
        )));
    }

    /**
     * Edit a category (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();
        $category = $this->getCategory($project);

        $values = $this->request->getValues();
        $values['project_id'] = $project['id'];
        $values['id'] = $category['id'];

        list($valid, $errors) = $this->categoryValidator->validateModification($values);

        if ($valid) {
            if ($this->categoryModel->update($values)) {
                $this->flash->success(t('This category has been updated successfully.'));
                return $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])));
            } else {
                $this->flash->failure(t('Unable to update this category.'));
            }
        }

        return $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a category
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $category = $this->getCategory($project);

        $this->response->html($this->helper->layout->project('category/remove', array(
            'project'  => $project,
            'category' => $category,
        )));
    }

    /**
     * Remove a category
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();
        $category = $this->getCategory($project);

        if ($this->categoryModel->remove($category['id'])) {
            $this->flash->success(t('Category removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this category.'));
        }

        $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])));
    }
}
