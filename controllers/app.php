<?php

namespace Controller;

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
