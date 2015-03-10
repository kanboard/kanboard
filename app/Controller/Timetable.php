<?php

namespace Controller;

use DateTime;

/**
 * Timetable controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Timetable extends User
{
    /**
     * Display timetable for the user
     *
     * @access public
     */
    public function index()
    {
        $user = $this->getUser();
        $from = $this->request->getStringParam('from', date('Y-m-d'));
        $to = $this->request->getStringParam('to', date('Y-m-d', strtotime('next week')));
        $timetable = $this->timetable->calculate($user['id'], new DateTime($from), new DateTime($to));

        $this->response->html($this->layout('timetable/index', array(
            'user' => $user,
            'timetable' => $timetable,
            'values' => array(
                'from' => $from,
                'to' => $to,
                'controller' => 'timetable',
                'action' => 'index',
                'user_id' => $user['id'],
            ),
        )));
    }
}
