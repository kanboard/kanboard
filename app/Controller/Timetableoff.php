<?php

namespace Controller;

/**
 * Time-off Timetable controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Timetableoff extends User
{
    protected $model = 'timetableOff';
    protected $controller_url = 'timetableoff';
    protected $template_dir = 'timetable_off';

    /**
     * Display timetable for the user
     *
     * @access public
     */
    public function index(array $values = array(), array $errors = array())
    {
        $user = $this->getUser();

        $paginator = $this->paginator
                ->setUrl($this->controller_url, 'index', array('user_id' => $user['id']))
                ->setMax(10)
                ->setOrder('date')
                ->setDirection('desc')
                ->setQuery($this->{$this->model}->getUserQuery($user['id']))
                ->calculate();

        $this->response->html($this->layout($this->template_dir.'/index', array(
            'values' => $values + array('user_id' => $user['id']),
            'errors' => $errors,
            'paginator' => $paginator,
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
        list($valid, $errors) = $this->{$this->model}->validateCreation($values);

        if ($valid) {

            if ($this->{$this->model}->create(
                    $values['user_id'],
                    $values['date'],
                    isset($values['all_day']) && $values['all_day'] == 1,
                    $values['start'],
                    $values['end'],
                    $values['comment'])) {

                $this->session->flash(t('Time slot created successfully.'));
                $this->response->redirect($this->helper->url($this->controller_url, 'index', array('user_id' => $values['user_id'])));
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

        $this->response->html($this->layout($this->template_dir.'/remove', array(
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

        if ($this->{$this->model}->remove($this->request->getIntegerParam('slot_id'))) {
            $this->session->flash(t('Time slot removed successfully.'));
        }
        else {
            $this->session->flash(t('Unable to remove this time slot.'));
        }

        $this->response->redirect($this->helper->url($this->controller_url, 'index', array('user_id' => $user['id'])));
    }
}
