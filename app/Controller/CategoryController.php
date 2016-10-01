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
     * Get the category (common method between actions)
     *
     * @access private
     * @return array
     * @throws PageNotFoundException
     */
    private function getCategory()
    {
        $category = $this->categoryModel->getById($this->request->getIntegerParam('category_id'));

        if (empty($category)) {
            throw new PageNotFoundException();
        }

        return $category;
    }

    /**
     * List of categories for a given project
     *
     * @access public
     * @param  array $values
     * @param  array $errors
     * @throws PageNotFoundException
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('category/index', array(
            'categories' => $this->categoryModel->getList($project['id'], false),
            'values' => $values + array('project_id' => $project['id']),
            'errors' => $errors,
            'project' => $project,
            'title' => t('Categories')
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
        list($valid, $errors) = $this->categoryValidator->validateCreation($values);

        if ($valid) {
            if ($this->categoryModel->create($values) !== false) {
                $this->flash->success(t('Your category have been created successfully.'));
                return $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])));
            } else {
                $this->flash->failure(t('Unable to create your category.'));
            }
        }

        return $this->index($values, $errors);
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
        $category = $this->getCategory();

        $this->response->html($this->helper->layout->project('category/edit', array(
            'values' => empty($values) ? $category : $values,
            'errors' => $errors,
            'project' => $project,
            'title' => t('Categories')
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

        $values = $this->request->getValues();
        list($valid, $errors) = $this->categoryValidator->validateModification($values);

        if ($valid) {
            if ($this->categoryModel->update($values)) {
                $this->flash->success(t('Your category have been updated successfully.'));
                return $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])));
            } else {
                $this->flash->failure(t('Unable to update your category.'));
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
        $category = $this->getCategory();

        $this->response->html($this->helper->layout->project('category/remove', array(
            'project' => $project,
            'category' => $category,
            'title' => t('Remove a category')
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
        $category = $this->getCategory();

        if ($this->categoryModel->remove($category['id'])) {
            $this->flash->success(t('Category removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this category.'));
        }

        $this->response->redirect($this->helper->url->to('CategoryController', 'index', array('project_id' => $project['id'])));
    }
}
