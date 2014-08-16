<?php

namespace Controller;

/**
 * Config controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Config extends Base
{
    /**
     * Display the settings page
     *
     * @access public
     */
    public function index()
    {
        $this->response->html($this->template->layout('config_index', array(
            'db_size' => $this->config->getDatabaseSize(),
            'user' => $_SESSION['user'],
            'user_projects' => $this->project->getAvailableList($this->acl->getUserId()),
            'notifications' => $this->notification->readSettings($this->acl->getUserId()),
            'languages' => $this->config->getLanguages(),
            'values' => $this->config->getAll(),
            'errors' => array(),
            'menu' => 'config',
            'title' => t('Settings'),
            'timezones' => $this->config->getTimezones(),
            'remember_me_sessions' => $this->authentication->backend('rememberMe')->getAll($this->acl->getUserId()),
            'last_logins' => $this->lastLogin->getAll($this->acl->getUserId()),
        )));
    }

    public function notifications()
    {
        $values = $this->request->getValues();
        $this->notification->saveSettings($this->acl->getUserId(), $values);
        $this->response->redirect('?controller=config#notifications');
    }

    /**
     * Validate and save settings
     *
     * @access public
     */
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
            'user_projects' => $this->project->getAvailableList($this->acl->getUserId()),
            'notifications' => $this->notification->readSettings($this->acl->getUserId()),
            'languages' => $this->config->getLanguages(),
            'values' => $values,
            'errors' => $errors,
            'menu' => 'config',
            'title' => t('Settings'),
            'timezones' => $this->config->getTimezones(),
            'remember_me_sessions' => $this->authentication->backend('rememberMe')->getAll($this->acl->getUserId()),
            'last_logins' => $this->lastLogin->getAll($this->acl->getUserId()),
        )));
    }

    /**
     * Download the Sqlite database
     *
     * @access public
     */
    public function downloadDb()
    {
        $this->checkCSRFParam();
        $this->response->forceDownload('db.sqlite.gz');
        $this->response->binary($this->config->downloadDatabase());
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     */
    public function optimizeDb()
    {
        $this->checkCSRFParam();
        $this->config->optimizeDatabase();
        $this->session->flash(t('Database optimization done.'));
        $this->response->redirect('?controller=config');
    }

    /**
     * Regenerate all application tokens
     *
     * @access public
     */
    public function tokens()
    {
        $this->checkCSRFParam();
        $this->config->regenerateTokens();
        $this->session->flash(t('All tokens have been regenerated.'));
        $this->response->redirect('?controller=config');
    }

    /**
     * Remove a "RememberMe" token
     *
     * @access public
     */
    public function removeRememberMeToken()
    {
        $this->checkCSRFParam();
        $this->authentication->backend('rememberMe')->remove($this->request->getIntegerParam('id'));
        $this->response->redirect('?controller=config&action=index#remember-me');
    }
}
