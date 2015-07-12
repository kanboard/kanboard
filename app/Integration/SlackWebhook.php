<?php

namespace Integration;

/**
 * Slack Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class SlackWebhook extends \Core\Base
{
    /**
     * Return true if Slack is enabled for this project or globally
     *
     * @access public
     * @param  integer  $project_id
     * @return boolean
     */
    public function isActivated($project_id)
    {
        return $this->config->get('integration_slack_webhook') == 1 || $this->projectIntegration->hasValue($project_id, 'slack', 1);
    }

    /**
     * Get wehbook url
     *
     * @access public
     * @param  integer  $project_id
     * @return string
     */
    public function getWebhookUrl($project_id)
    {
        if ($this->config->get('integration_slack_webhook') == 1) {
            return $this->config->get('integration_slack_webhook_url');
        }

        $options = $this->projectIntegration->getParameters($project_id);
        return $options['slack_webhook_url'];
    }

    /**
     * Get optional Slack channel
     *
     * @access public
     * @param  integer  $project_id
     * @return string
     */
    public function getChannel($project_id)
    {
        $channel = $this->config->get('integration_slack_webhook_channel');

        if (! empty($channel)) {
            return $channel;
    	}

    	$options = $this->projectIntegration->getParameters($project_id);
    	return $options['slack_webhook_channel'];
    }

    /**
     * Send message to the incoming Slack webhook
     *
     * @access public
     * @param  integer     $project_id      Project id
     * @param  integer     $task_id         Task id
     * @param  string      $event_name      Event name
     * @param  array       $event           Event data
     */
    public function notify($project_id, $task_id, $event_name, array $event)
    {
        if ($this->isActivated($project_id)) {

            $project = $this->project->getbyId($project_id);

            $event['event_name'] = $event_name;
            $event['author'] = $this->user->getFullname($this->session['user']);

            $payload = array(
                'text' => '*['.$project['name'].']* '.str_replace('&quot;', '"', $this->projectActivity->getTitle($event)).(isset($event['task']['title']) ? ' ('.$event['task']['title'].')' : ''),
                'username' => 'Kanboard',
                'icon_url' => 'http://kanboard.net/assets/img/favicon.png',
            );

            if ($this->config->get('application_url')) {
                $payload['text'] .= ' - <'.$this->helper->url->href('task', 'show', array('task_id' => $task_id, 'project_id' => $project_id), false, '', true);
                $payload['text'] .= '|'.t('view the task on Kanboard').'>';
            }

            $channel = $this->getChannel($project_id);
            if (! empty($channel)) {
            	$payload['channel'] = $channel;
            }

            $this->httpClient->postJson($this->getWebhookUrl($project_id), $payload);
        }
    }
}
