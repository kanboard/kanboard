<?php

namespace Controller;

/**
 * Custom Filter management
 *
 * @package controller
 * @author  Timo Litzbarski
 */
class Customfilter extends Base
{
    /**
     * Custom Filters list
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('customfilter/index', array(
            'values' => $values + array('project_id' => $project['id']),
            'custom_filters' => $this->customFilter->getAll($project['id'],$this->userSession->getId()),
            'title' => t('Edit custom filters'),
            'project' => $project,
            'errors' => $errors,
            'user_id' => $this->userSession->getId(),
        )));
    }
    
    /**
     * save a new custom filter
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();

        $values = $this->request->getValues();
        $values['user_id'] = $this->userSession->getId();
        
        //list($valid, $errors) = $this->customFilter->validateCreation($values);

        //if ($valid) {

            if ($this->customFilter->create($values)) {
                $this->session->flash(t('Your custom filter have been created successfully.'));
                $this->response->redirect($this->helper->url->to('customfilter', 'index', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to create your custom filter.'));
            }
       // }

        $this->index($values, $errors);
    }
    
    /**
     * Remove a custom filter
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        
        $filter = $this->request->getStringParam('filter');
        $project_id = $this->request->getIntegerParam('project_id');
        $user_id = $this->request->getIntegerParam('user_id');
        
        //$custom_filter = $this->customFilter->getCustomFilter($filter,$project_id,$user_id);

        if ($this->customFilter->remove($filter,$project_id,$user_id)) {
            $this->session->flash(t('Custom filter removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this custom filter.'));
        }

        $this->response->redirect($this->helper->url->to('customfilter', 'index', array('project_id' => $project_id)));
    }
    
    /**
     * Edit a custom filter (display the form)
     *
     * @access public
     */
    public function edit(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();
        
        $filter = $this->request->getStringParam('filter');
        $project_id = $this->request->getIntegerParam('project_id');
        $user_id = $this->request->getIntegerParam('user_id');
        $custom_filter = $this->customFilter->getCustomFilter($filter,$project_id,$user_id);
        $custom_filter['filter_original'] = $custom_filter['filter'];

        $this->response->html($this->projectLayout('customfilter/edit', array(
            'values' => empty($values) ? $custom_filter : $values,
            'errors' => $errors,
            'project' => $project,
            'custom_filter' => $custom_filter,
            'title' => t('Edit custom filter')
        )));
    }
    
    /**
     * Edit a custom filter (validate the form and update the database)
     *
     * @access public
     */
    public function update()
    {
        $project = $this->getProject();

        $values = $this->request->getValues();
        $filter_original = $values['filter_original'];
        
        unset($values['filter_original']);
        
        if (!isset($values['is_shared'])) {
                $values += array('is_shared' => 0);
        }
        
        //list($valid, $errors) = $this->customFilter->validateModification($values);

        //if ($valid) {

            if ($this->customFilter->update($values,$filter_original)) {
                $this->session->flash(t('Your custom filter have been updated successfully.'));
                $this->response->redirect($this->helper->url->to('customfilter', 'index', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to update custom filter.'));
            }
        //}

        $values['filter'] = $filter_original;
        $this->edit($values, $errors);
    }

}