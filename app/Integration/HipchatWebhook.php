<?php

namespace Integration;

/**
 * Hipchat webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class HipchatWebhook extends Base
{
    /**
     * Return true if Hipchat is enabled for this project or globally
     *
     * @access public
     * @param  integer  $project_id
     * @return boolean
     */
    public function isActivated($project_id)
    {
        return $this->config->get('integration_hipchat') == 1 || $this->projectIntegration->hasValue($project_id, 'hipchat', 1);
    }

    /**
     * Get API parameters
     *
     * @access public
     * @param  integer  $project_id
     * @return array
     */
    public function getParameters($project_id)
    {
        if ($this->config->get('integration_hipchat') == 1) {
            return array(
                'api_url' => $this->config->get('integration_hipchat_api_url'),
                'room_id' => $this->config->get('integration_hipchat_room_id'),
                'room_token' => $this->config->get('integration_hipchat_room_token'),
            );
        }

        $options = $this->projectIntegration->getParameters($project_id);

        return array(
            'api_url' => $options['hipchat_api_url'],
            'room_id' => $options['hipchat_room_id'],
            'room_token' => $options['hipchat_room_token'],
        );
    }

    /**
     * Send the notification if activated
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

            $params = $this->getParameters($project_id);
            $project = $this->project->getbyId($project_id);

            $event['event_name'] = $event_name;
            $event['author'] = $this->user->getFullname($this->session['user']);

            $html = '<img src="http://kanboard.net/assets/img/favicon-32x32.png"/>';
            $html .= '<strong>'.$project['name'].'</strong>'.(isset($event['task']['title']) ? '<br/>'.$event['task']['title'] : '').'<br/>';
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
                $params['api_url'],
                $params['room_id'],
                $params['room_token']
            );

            $this->httpClient->post($url, $payload);
        }
    }
}
