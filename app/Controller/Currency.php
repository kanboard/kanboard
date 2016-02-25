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
     * Display all currency rates and form
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $this->response->html($this->helper->layout->config('currency/index', array(
            'config_values' => array('application_currency' => $this->config->get('application_currency')),
            'values' => $values,
            'errors' => $errors,
            'rates' => $this->currency->getAll(),
            'currencies' => $this->currency->getCurrencies(),
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
        list($valid, $errors) = $this->currencyValidator->validateCreation($values);

        if ($valid) {
            if ($this->currency->create($values['currency'], $values['rate'])) {
                $this->flash->success(t('The currency rate have been added successfully.'));
                $this->response->redirect($this->helper->url->to('currency', 'index'));
            } else {
                $this->flash->failure(t('Unable to add this currency rate.'));
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
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('currency', 'index'));
    }
}
