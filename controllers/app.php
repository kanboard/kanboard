<?php

namespace Controller;

require_once __DIR__.'/Base.php';

class App extends Base
{
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
