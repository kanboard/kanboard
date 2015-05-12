<?php

namespace Integration;

use Exception;
use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Message;
use Fabiang\Xmpp\Protocol\Presence;

/**
 * Jabber
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class Jabber extends Base
{
    /**
     * Return true if Jabber is enabled for this project or globally
     *
     * @access public
     * @param  integer  $project_id
     * @return boolean
     */
    public function isActivated($project_id)
    {
        return $this->config->get('integration_jabber') == 1 || $this->projectIntegration->hasValue($project_id, 'jabber', 1);
    }

    /**
     * Get connection parameters
     *
     * @access public
     * @param  integer  $project_id
     * @return array
     */
    public function getParameters($project_id)
    {
        if ($this->config->get('integration_jabber') == 1) {
            return array(
                'server' => $this->config->get('integration_jabber_server'),
                'domain' => $this->config->get('integration_jabber_domain'),
                'username' => $this->config->get('integration_jabber_username'),
                'password' => $this->config->get('integration_jabber_password'),
                'nickname' => $this->config->get('integration_jabber_nickname'),
                'room' => $this->config->get('integration_jabber_room'),
            );
        }

        $options = $this->projectIntegration->getParameters($project_id);

        return array(
            'server' => $options['jabber_server'],
            'domain' => $options['jabber_domain'],
            'username' => $options['jabber_username'],
            'password' => $options['jabber_password'],
            'nickname' => $options['jabber_nickname'],
            'room' => $options['jabber_room'],
        );
    }

    /**
     * Build and send the message
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

            $payload = '['.$project['name'].'] '.str_replace('&quot;', '"', $this->projectActivity->getTitle($event)).(isset($event['task']['title']) ? ' ('.$event['task']['title'].')' : '');

            if ($this->config->get('application_url')) {
                $payload .= ' '.$this->config->get('application_url');
                $payload .= $this->helper->url('task', 'show', array('task_id' => $task_id, 'project_id' => $project_id));
            }

            $this->sendMessage($project_id, $payload);
        }
    }

    /**
     * Send message to the XMPP server
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $payload
     */
    public function sendMessage($project_id, $payload)
    {
        try {

            $params = $this->getParameters($project_id);

            $options = new Options($params['server']);
            $options->setUsername($params['username']);
            $options->setPassword($params['password']);
            $options->setTo($params['domain']);

            $client = new Client($options);

            $channel = new Presence;
            $channel->setTo($params['room'])->setNickName($params['nickname']);
            $client->send($channel);

            $message = new Message;
            $message->setMessage($payload)
                    ->setTo($params['room'])
                    ->setType(Message::TYPE_GROUPCHAT);

            $client->send($message);

            $client->disconnect();
        }
        catch (Exception $e) {
            $this->container['logger']->error('Jabber error: '.$e->getMessage());
        }
    }
}
