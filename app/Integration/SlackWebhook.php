<?php

namespace Integration;

/**
 * Slack Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class SlackWebhook extends Base
{
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
        $project = $this->project->getbyId($project_id);

        $event['event_name'] = $event_name;
        $event['author'] = $this->user->getFullname($this->session['user']);

        $payload = array(
            'text' => '*['.$project['name'].']* '.str_replace('&quot;', '"', $this->projectActivity->getTitle($event)),
            'username' => 'Kanboard',
            'icon_url' => 'http://kanboard.net/assets/img/favicon.png',
        );

        if ($this->config->get('application_url')) {
            $payload['text'] .= ' - <'.$this->config->get('application_url');
            $payload['text'] .= $this->helper->u('task', 'show', array('task_id' => $task_id, 'project_id' => $project_id));
            $payload['text'] .= '|'.t('view the task on Kanboard').'>';
        }

        $this->httpClient->post($this->config->get('integration_slack_webhook_url'), $payload);
    }
}
