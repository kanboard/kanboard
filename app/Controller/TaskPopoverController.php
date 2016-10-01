<?php

namespace Kanboard\Controller;

/**
 * Task Popover
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskPopoverController extends BaseController
{
    /**
     * Screenshot popover
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        $this->response->html($this->template->render('task_file/screenshot', array(
            'task' => $task,
        )));
    }
}
