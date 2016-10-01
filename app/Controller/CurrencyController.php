<?php

namespace Kanboard\Controller;

/**
 * Currency Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class CurrencyController extends BaseController
{
    /**
     * Display all currency rates and form
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function index(array $values = array(), array $errors = array())
    {
        $this->response->html($this->helper->layout->config('currency/index', array(
            'config_values' => array('application_currency' => $this->configModel->get('application_currency')),
            'values' => $values,
            'errors' => $errors,
            'rates' => $this->currencyModel->getAll(),
            'currencies' => $this->currencyModel->getCurrencies(),
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
            if ($this->currencyModel->create($values['currency'], $values['rate'])) {
                $this->flash->success(t('The currency rate have been added successfully.'));
                return $this->response->redirect($this->helper->url->to('CurrencyController', 'index'));
            } else {
                $this->flash->failure(t('Unable to add this currency rate.'));
            }
        }

        return $this->index($values, $errors);
    }

    /**
     * Save reference currency
     *
     * @access public
     */
    public function reference()
    {
        $values = $this->request->getValues();

        if ($this->configModel->save($values)) {
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('CurrencyController', 'index'));
    }
}
