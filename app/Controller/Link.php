<?php
namespace Controller;

/**
 * Link controller
 *
 * @package controller
 * @author Olivier Maridat
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
    
        if (isset($params['values']['project_id']) && -1 != $params['values']['project_id']) {
            return $this->projectLayout($template, $params);
        }
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
        $link = $this->link->getById($this->request->getIntegerParam('link_id'), $this->request->getIntegerParam('project_id', -1));
        if (! $link) {
            $this->notfound();
        }
        $link['link_id'] = $link[0]['link_id'];
        $link['project_id'] = $link[0]['project_id'];
        return $link;
    }

    /**
     * Method to get a project
     *
     * @access protected
     * @param  integer      $project_id    Default project id
     * @return array
     */
    protected function getProject($project_id = -1)
    {
        $project = array('id' => $project_id);
        $project_id = $this->request->getIntegerParam('project_id', $project_id);
        if (-1 != $project_id) {
            $project = parent::getProject($project_id);
        }
        return $project;
    }

    /**
     * List of links for a given project
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        $values['project_id'] = $project['id'];
        $values[] = array();
        
        $this->response->html($this->layout('link/index', array(
            'links' => $this->link->getMergedList($project['id']),
            'values' => $values,
            'errors' => $errors,
            'project' => $project,
            'title' => t('Settings').' &gt; '.t('Board\'s links settings'),
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
            if ($this->link->create($values)) {
                $this->session->flash(t('Link added successfully.'));
                $this->response->redirect('?controller=link&action=index&project_id='.$values['project_id']);
            }
            else {
                $this->session->flashError(t('Unable to create your link.'));
            }
        }
		if (!empty($values)) {
        	$this->link->prepare($values);
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
        $project = $this->getProject();
        
        $this->response->html($this->layout('link/edit', array(
            'values' => empty($values) ? $this->getLink() : $values,
            'errors' => $errors,
            'project' => $project,
            'edit' => true,
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
        $values = $this->request->getValues();
        list($valid, $errors) = $this->link->validateModification($values);
        
        if ($valid) {
            if ($this->link->update($values)) {
                $this->session->flash(t('Link updated successfully.'));
                $this->response->redirect('?controller=link&action=index&project_id='.$values['project_id']);
            }
            else {
                $this->session->flashError(t('Unable to update your link.'));
            }
        }
        if (!empty($values)) {
        	$this->link->prepare($values);
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
        $project = $this->getProject();
        $link = $this->getLink();
        
        $this->response->html($this->layout('link/remove', array(
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
        $link = $this->getLink();
        
        if ($this->link->remove($link['link_id'])) {
            $this->session->flash(t('Link removed successfully.'));
            $this->response->redirect('?controller=link&action=index&project_id='.$link['project_id']);
        }
        else {
            $this->session->flashError(t('Unable to remove this link.'));
        }
        $this->confirm();
    }
}
