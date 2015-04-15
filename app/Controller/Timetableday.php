<?php

namespace Controller;

/**
 * Day Timetable controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Timetableday extends User
{
    /**
     * Display timetable for the user
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $user = $this->getUser();

        $this->response->html($this->layout('timetable_day/index', array(
            'timetable' => $this->timetableDay->getByUser($user['id']),
            'values' => $values + array('user_id' => $user['id']),
            'errors' => $errors,
            'user' => $user,
        )));
    }

    /**
     * Validate and save
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->timetableDay->validateCreation($values);

        if ($valid) {

            if ($this->timetableDay->create($values['user_id'], $values['start'], $values['end'])) {
                $this->session->flash(t('Time slot created successfully.'));
                $this->response->redirect($this->helper->url('timetableday', 'index', array('user_id' => $values['user_id'])));
            }
            else {
                $this->session->flashError(t('Unable to save this time slot.'));
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

        $this->response->html($this->layout('timetable_day/remove', array(
            'slot_id' => $this->request->getIntegerParam('slot_id'),
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

        if ($this->timetableDay->remove($this->request->getIntegerParam('slot_id'))) {
            $this->session->flash(t('Time slot removed successfully.'));
        }
        else {
            $this->session->flash(t('Unable to remove this time slot.'));
        }

        $this->response->redirect($this->helper->url('timetableday', 'index', array('user_id' => $user['id'])));
    }
}
