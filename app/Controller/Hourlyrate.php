<?php

namespace Controller;

/**
 * Hourly Rate controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Hourlyrate extends User
{
    /**
     * Display rate and form
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $user = $this->getUser();

        $this->response->html($this->layout('hourlyrate/index', array(
            'rates' => $this->hourlyRate->getAllByUser($user['id']),
            'currencies_list' => $this->config->getCurrencies(),
            'values' => $values + array('user_id' => $user['id']),
            'errors' => $errors,
            'user' => $user,
        )));
    }

    /**
     * Validate and save a new rate
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->hourlyRate->validateCreation($values);

        if ($valid) {

            if ($this->hourlyRate->create($values['user_id'], $values['rate'], $values['currency'], $values['date_effective'])) {
                $this->session->flash(t('Hourly rate created successfully.'));
                $this->response->redirect($this->helper->url('hourlyrate', 'index', array('user_id' => $values['user_id'])));
            }
            else {
                $this->session->flashError(t('Unable to save the hourly rate.'));
            }
        }

        $this->index($values, $errors);
    }

    /**
     * Confirmation dialag box to remove a row
     *
     * @access public
     */
    public function confirm()
    {
        $user = $this->getUser();

        $this->response->html($this->layout('hourlyrate/remove', array(
            'rate_id' => $this->request->getIntegerParam('rate_id'),
            'user' => $user,
        )));
    }

    /**
     * Remove a row
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();

        if ($this->hourlyRate->remove($this->request->getIntegerParam('rate_id'))) {
            $this->session->flash(t('Rate removed successfully.'));
        }
        else {
            $this->session->flash(t('Unable to remove this rate.'));
        }

        $this->response->redirect($this->helper->url('hourlyrate', 'index', array('user_id' => $user['id'])));
    }
}
