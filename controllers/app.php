<?php

namespace Controller;

require_once __DIR__.'/base.php';

/**
 * Application controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class App extends Base
{
    /**
     * Redirect to the project creation page or the board controller
     *
     * @access public
     */
    public function index()
    {
        if ($this->project->countByStatus(\Model\Project::ACTIVE)) {
            $this->response->redirect('?controller=board');
        }
        else {
            $this->redirectNoProject();
        }
    }
}
