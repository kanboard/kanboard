<?php

namespace Controller;

require_once __DIR__.'/Base.php';

class Config extends Base
{
    // Settings page
    public function index()
    {
        $this->response->html($this->template->layout('config_index', array(
            'db_size' => $this->config->getDatabaseSize(),
            'user' => $_SESSION['user'],
            'projects' => $this->project->getList(),
            'languages' => $this->config->getLanguages(),
            'values' => $this->config->getAll(),
            'errors' => array(),
            'menu' => 'config',
            'title' => t('Settings'),
            'timezones' => $this->config->getTimezones()
        )));
    }

    // Validate and save settings
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->config->validateModification($values);

        if ($valid) {

            if ($this->config->save($values)) {
                $this->config->reload();
                $this->session->flash(t('Settings saved successfully.'));
            } else {
                $this->session->flashError(t('Unable to save your settings.'));
            }

            $this->response->redirect('?controller=config');
        }

        $this->response->html($this->template->layout('config_index', array(
            'db_size' => $this->config->getDatabaseSize(),
            'user' => $_SESSION['user'],
            'projects' => $this->project->getList(),
            'languages' => $this->config->getLanguages(),
            'values' => $values,
            'errors' => $errors,
            'menu' => 'config',
            'title' => t('Settings'),
            'timezones' => $this->config->getTimezones()
        )));
    }

    // Download the database
    public function downloadDb()
    {
        $this->response->forceDownload('db.sqlite.gz');
        $this->response->binary($this->config->downloadDatabase());
    }

    // Optimize the database
    public function optimizeDb()
    {
        $this->config->optimizeDatabase();
        $this->session->flash(t('Database optimization done.'));
        $this->response->redirect('?controller=config');
    }
}
