<?php

namespace Controller;

/**
 * Budget
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Budget extends Base
{
    /**
     * Budget index page
     *
     * @access public
     */
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('budget/index', array(
            'daily_budget' => $this->budget->getDailyBudgetBreakdown($project['id']),
            'project' => $project,
            'title' => t('Budget')
        )));
    }

    /**
     * Cost breakdown by users/subtasks/tasks
     *
     * @access public
     */
    public function breakdown()
    {
        $project = $this->getProject();

        $paginator = $this->paginator
            ->setUrl('budget', 'breakdown', array('project_id' => $project['id']))
            ->setMax(30)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->budget->getSubtaskBreakdown($project['id']))
            ->calculate();

        $this->response->html($this->projectLayout('budget/breakdown', array(
            'paginator' => $paginator,
            'project' => $project,
            'title' => t('Budget')
        )));
    }

    /**
     * Create budget lines
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        if (empty($values)) {
            $values['date'] = date('Y-m-d');
        }

        $this->response->html($this->projectLayout('budget/create', array(
            'lines' => $this->budget->getAll($project['id']),
            'values' => $values + array('project_id' => $project['id']),
            'errors' => $errors,
            'project' => $project,
            'title' => t('Budget')
        )));
    }

    /**
     * Validate and save a new budget
     *
     * @access public
     */
    public function save()
    {
        $project = $this->getProject();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->budget->validateCreation($values);

        if ($valid) {

            if ($this->budget->create($values['project_id'], $values['amount'], $values['comment'], $values['date'])) {
                $this->session->flash(t('The budget line have been created successfully.'));
                $this->response->redirect($this->helper->url('budget', 'create', array('project_id' => $project['id'])));
            }
            else {
                $this->session->flashError(t('Unable to create the budget line.'));
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Confirmation dialog before removing a budget
     *
     * @access public
     */
    public function confirm()
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('budget/remove', array(
            'project' => $project,
            'budget_id' => $this->request->getIntegerParam('budget_id'),
            'title' => t('Remove a budget line'),
        )));
    }

    /**
     * Remove a budget
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $project = $this->getProject();

        if ($this->budget->remove($this->request->getIntegerParam('budget_id'))) {
            $this->session->flash(t('Budget line removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this budget line.'));
        }

        $this->response->redirect($this->helper->url('budget', 'create', array('project_id' => $project['id'])));
    }
}
