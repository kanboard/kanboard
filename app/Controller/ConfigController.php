<?php

namespace Kanboard\Controller;

/**
 * Config Controller
 *
 * @package  Kanboard/Controller
 * @author   Frederic Guillot
 */
class ConfigController extends BaseController
{
    /**
     * Display the about page
     *
     * @access public
     */
    public function index()
    {
        $this->response->html($this->helper->layout->config('config/about', array(
            'db_size' => $this->configModel->getDatabaseSize(),
            'db_version' => $this->db->getDriver()->getDatabaseVersion(),
            'user_agent' => $this->request->getServerVariable('HTTP_USER_AGENT'),
            'title' => t('Settings').' &gt; '.t('About'),
        )));
    }

    /**
     * Save settings
     *
     */
    public function save()
    {
        $values =  $this->request->getValues();
        $redirect = $this->request->getStringParam('redirect', 'application');

        switch ($redirect) {
            case 'application':
                $values += array('password_reset' => 0);
                break;
            case 'project':
                $values += array(
                    'subtask_restriction' => 0,
                    'subtask_time_tracking' => 0,
                    'cfd_include_closed_tasks' => 0,
                    'disable_private_project' => 0,
                );
                break;
            case 'integrations':
                $values += array('integration_gravatar' => 0);
                break;
            case 'calendar':
                $values += array('calendar_user_subtasks_time_tracking' => 0);
                break;
        }

        if ($this->configModel->save($values)) {
            $this->languageModel->loadCurrentLanguage();
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('ConfigController', $redirect));
    }

    /**
     * Display the application settings page
     *
     * @access public
     */
    public function application()
    {
        $this->response->html($this->helper->layout->config('config/application', array(
            'mail_transports' => $this->emailClient->getAvailableTransports(),
            'languages' => $this->languageModel->getLanguages(),
            'timezones' => $this->timezoneModel->getTimezones(),
            'date_formats' => $this->dateParser->getAvailableFormats($this->dateParser->getDateFormats()),
            'datetime_formats' => $this->dateParser->getAvailableFormats($this->dateParser->getDateTimeFormats()),
            'time_formats' => $this->dateParser->getAvailableFormats($this->dateParser->getTimeFormats()),
            'title' => t('Settings').' &gt; '.t('Application settings'),
        )));
    }

    /**
     * Display the email settings page
     *
     * @access public
     */
    public function email()
    {
        $values = $this->configModel->getAll();

        if (empty($values['mail_transport'])) {
            $values['mail_transport'] = MAIL_TRANSPORT;
        }

        $this->response->html($this->helper->layout->config('config/email', array(
            'values' => $values,
            'mail_transports' => $this->emailClient->getAvailableTransports(),
            'title' => t('Settings').' &gt; '.t('Email settings'),
        )));
    }

    /**
     * Display the project settings page
     *
     * @access public
     */
    public function project()
    {
        $this->response->html($this->helper->layout->config('config/project', array(
            'colors' => $this->colorModel->getList(),
            'default_columns' => implode(', ', $this->boardModel->getDefaultColumns()),
            'title' => t('Settings').' &gt; '.t('Project settings'),
        )));
    }

    /**
     * Display the board settings page
     *
     * @access public
     */
    public function board()
    {
        $this->response->html($this->helper->layout->config('config/board', array(
            'title' => t('Settings').' &gt; '.t('Board settings'),
        )));
    }

    /**
     * Display the calendar settings page
     *
     * @access public
     */
    public function calendar()
    {
        $this->response->html($this->helper->layout->config('config/calendar', array(
            'title' => t('Settings').' &gt; '.t('Calendar settings'),
        )));
    }

    /**
     * Display the integration settings page
     *
     * @access public
     */
    public function integrations()
    {
        $this->response->html($this->helper->layout->config('config/integrations', array(
            'title' => t('Settings').' &gt; '.t('Integrations'),
        )));
    }

    /**
     * Display the webhook settings page
     *
     * @access public
     */
    public function webhook()
    {
        $this->response->html($this->helper->layout->config('config/webhook', array(
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
        $this->response->html($this->helper->layout->config('config/api', array(
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
        $this->response->withFileDownload('db.sqlite.gz');
        $this->response->binary($this->configModel->downloadDatabase());
    }

    /**
     * Optimize the Sqlite database
     *
     * @access public
     */
    public function optimizeDb()
    {
        $this->checkCSRFParam();
        $this->configModel->optimizeDatabase();
        $this->flash->success(t('Database optimization done.'));
        $this->response->redirect($this->helper->url->to('ConfigController', 'index'));
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
        $this->configModel->regenerateToken($type.'_token');

        $this->flash->success(t('Token regenerated.'));
        $this->response->redirect($this->helper->url->to('ConfigController', $type));
    }
}
