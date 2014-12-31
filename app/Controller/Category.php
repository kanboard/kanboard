<?php

namespace Controller;

/**
 * Category management
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Category extends Base
{
    /**
     * Get the category (common method between actions)
     *
     * @access private
     * @param  integer   $project_id
     * @return array
     */
    private function getCategory($project_id)
    {
        $category = $this->category->getById($this->request->getIntegerParam('category_id'));

        if (! $category) {
            $this->session->flashError(t('Category not found.'));
            $this->response->redirect('?controller=category&action=index&project_id='.$project_id);
        }

        return $category;
    }

    /**
     * List of categories for a given project
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('category/index', array(
            'categories' => $this->category->getList($project['id'], false),
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
        list($valid, $errors) = $this->category->validateCreation($values);

        if ($valid) {

            if ($this->category->create($values)) {
                $this->session->flash(t('Your category have been created successfully.'));
                $this->response->redirect('?controller=category&action=index&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to create your category.'));
            }
        }

        $this->index($values, $errors);
    }

    /**
     * Edit a category (display the form)
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $category = $this->getCategory($project['id']);

        $this->response->html($this->projectLayout('category/edit', array(
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
        list($valid, $errors) = $this->category->validateModification($values);

        if ($valid) {

            if ($this->category->update($values)) {
                $this->session->flash(t('Your category have been updated successfully.'));
                $this->response->redirect('?controller=category&action=index&project_id='.$project['id']);
            }
            else {
                $this->session->flashError(t('Unable to update your category.'));
            }
        }

        $this->edit($values, $errors);
    }

    /**
     * Confirmation dialog before removing a category
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();
        $category = $this->getCategory($project['id']);

        $this->response->html($this->projectLayout('category/remove', array(
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
        $category = $this->getCategory($project['id']);

        if ($this->category->remove($category['id'])) {
            $this->session->flash(t('Category removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this category.'));
        }

        $this->response->redirect('?controller=category&action=index&project_id='.$project['id']);
    }
}
