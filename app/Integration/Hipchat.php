<?php

namespace Integration;

/**
 * Hipchat Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class Hipchat extends Base
{
    /**
     * Send message to the Hipchat room
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

        $html = '<img src="http://kanboard.net/assets/img/favicon-32x32.png"/>';
        $html .= '<strong>'.$project['name'].'</strong><br/>';
        $html .= $this->projectActivity->getTitle($event);

        if ($this->config->get('application_url')) {
            $html .= '<br/><a href="'.$this->config->get('application_url');
            $html .= $this->helper->u('task', 'show', array('task_id' => $task_id, 'project_id' => $project_id)).'">';
            $html .= t('view the task on Kanboard').'</a>';
        }

        $payload = array(
            'message' => $html,
            'color' => 'yellow',
        );

        $url = sprintf(
            '%s/v2/room/%s/notification?auth_token=%s',
            $this->config->get('integration_hipchat_api_url'),
            $this->config->get('integration_hipchat_room_id'),
            $this->config->get('integration_hipchat_room_token')
        );

        $this->httpClient->post($url, $payload);
    }
}
