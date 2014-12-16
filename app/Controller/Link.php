<?php

namespace Controller;

/**
 * Link controller
 *
 * @package  controller
 * @author   Olivier Maridat
 */
class Link extends Base
{
    /**
     * Get the current link
     *
     * @access private
     * @return array
     */
    private function getLink()
    {
        $link = $this->link->getById($this->request->getIntegerParam('link_id'));

        if (! $link) {
            $this->notfound();
        }

        return $link;
    }
    
    /**
     * Get the current link
     *
     * @access private
     * @return array
     */
    private function getMergedLink()
    {
    	$link = $this->link->getMergedById($this->request->getIntegerParam('link_id'));
    
    	if (! $link) {
    		$this->notfound();
    	}
    
    	return $link;
    }
    
    /**
     * List of links for a given project
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
    	$project = $this->getProjectManagement();
    	$this->response->html($this->projectLayout('link/index', array(
    		'links' => $this->link->getMergedList($project['id']),
    		'values' => $values + array('project_id' => $project['id']),
    		'errors' => $errors,
    		'project' => $project,
    		'title' => t('Links')
    	)));
    }

    /**
     * Validate and save a new link
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProjectManagement();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateCreation($values);

        if ($valid) {
            if ($this->link->create($values)) {
                $this->session->flash(t('Your link have been created successfully.'));
                $this->response->redirect('?controller=link&action=index&project_id='.$project['id']);
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
        $project = $this->getProjectManagement();
        $link = $this->getMergedLink();//($project['id']);

        $this->response->html($this->projectLayout('link/edit', array(
            'values' => empty($values) ? $link : $values,
            'errors' => $errors,
            'project' => $project,
            'title' => t('Links')
        )));
    }

    /**
     * Edit a link (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProjectManagement();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateModification($values);

        if ($valid) {

            if ($this->link->update($values)) {
                $this->session->flash(t('Your link have been updated successfully.'));
                $this->response->redirect('?controller=link&action=index&project_id='.$project['id']);
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
    	$project = $this->getProjectManagement();
    	$link = $this->getLink($project['id']);
    
    	$this->response->html($this->projectLayout('link/remove', array(
    		'project' => $project,
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
    	$project = $this->getProjectManagement();
    	$link = $this->getLink($project['id']);
    
    	if ($this->link->remove($link['id'])) {
    		$this->session->flash(t('Link removed successfully.'));
    	} else {
    		$this->session->flashError(t('Unable to remove this link.'));
    	}
    
    	$this->response->redirect('?controller=link&action=index&project_id='.$project['id']);
    }
}
