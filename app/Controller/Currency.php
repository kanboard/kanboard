<?php

namespace Kanboard\Controller;

/**
 * Currency controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Currency extends Base
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

        return $this->template->layout('config/layout', $params);
    }

    /**
     * Display all currency rates and form
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $this->response->html($this->layout('currency/index', array(
            'config_values' => array('application_currency' => $this->config->get('application_currency')),
            'values' => $values,
            'errors' => $errors,
            'rates' => $this->currency->getAll(),
            'currencies' => $this->config->getCurrencies(),
            'title' => t('Settings').' &gt; '.t('Currency rates'),
        )));
    }

    /**
     * Validate and save a new currency rate
     *
     * @access public
     */
    public function create()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->currency->validate($values);

        if ($valid) {
            if ($this->currency->create($values['currency'], $values['rate'])) {
                $this->session->flash(t('The currency rate have been added successfully.'));
                $this->response->redirect($this->helper->url->to('currency', 'index'));
            } else {
                $this->session->flashError(t('Unable to add this currency rate.'));
            }
        }

        $this->index($values, $errors);
    }

    /**
     * Save reference currency
     *
     * @access public
     */
    public function reference()
    {
        $values = $this->request->getValues();

        if ($this->config->save($values)) {
            $this->config->reload();
            $this->session->flash(t('Settings saved successfully.'));
        } else {
            $this->session->flashError(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('currency', 'index'));
    }
}
