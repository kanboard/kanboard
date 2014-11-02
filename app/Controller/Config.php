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
     * Common layout for config views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());
        $params['values'] = $this->config->getAll();
        $params['errors'] = array();
        $params['config_content_for_layout'] = $this->template->load($template, $params);

        return $this->template->layout('config_layout', $params);
    }

    /**
     * Common method between pages
     *
     * @access private
     * @param  string     $redirect    Action to redirect after saving the form
     */
    private function common($redirect)
    {
        if ($this->request->isPost()) {

            $values = $this->request->getValues();

            if ($this->config->save($values)) {
                $this->config->reload();
                $this->session->flash(t('Settings saved successfully.'));
            }
            else {
                $this->session->flashError(t('Unable to save your settings.'));
            }

            $this->response->redirect('?controller=config&action='.$redirect);
        }
    }

    /**
     * Display the about page
     *
     * @access public
     */
    public function index()
    {
        $this->response->html($this->layout('config_about', array(
            'db_size' => $this->config->getDatabaseSize(),
            'title' => t('Settings').' &gt; '.t('About'),
        )));
    }

    /**
     * Display the application settings page
     *
     * @access public
     */
    public function application()
    {
        $this->common('application');

        $this->response->html($this->layout('config_application', array(
            'languages' => $this->config->getLanguages(),
            'timezones' => $this->config->getTimezones(),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'title' => t('Settings').' &gt; '.t('Application settings'),
        )));
    }

    /**
     * Display the board settings page
     *
     * @access public
     */
    public function board()
    {
        $this->common('board');

        $this->response->html($this->layout('config_board', array(
            'default_columns' => implode(', ', $this->board->getDefaultColumns()),
            'title' => t('Settings').' &gt; '.t('Board settings'),
        )));
    }

    /**
     * Display the webhook settings page
     *
     * @access public
     */
    public function webhook()
    {
        $this->common('webhook');

        $this->response->html($this->layout('config_webhook', array(
            'title' => t('Settings').' &gt; '.t('Webhook settings'),
        )));
    }

    /**
     * Display the api settings page
     *
     * @access public
     */
    public function api()
    {
        $this->response->html($this->layout('config_api', array(
            'title' => t('Settings').' &gt; '.t('API'),
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
     * Regenerate webhook token
     *
     * @access public
     */
    public function token()
    {
        $type = $this->request->getStringParam('type');

        $this->checkCSRFParam();
        $this->config->regenerateToken($type.'_token');

        $this->session->flash(t('Token regenerated.'));
        $this->response->redirect('?controller=config&action='.$type);
    }
}
