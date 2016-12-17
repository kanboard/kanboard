<?php

namespace Kanboard\Job;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\UserModel;

/**
 * Class UserMentionJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class UserMentionJob extends BaseJob
{
    /**
     * Set job parameters
     *
     * @param  string       $text
     * @param  string       $eventName
     * @param  GenericEvent $event
     * @return $this
     */
    public function withParams($text, $eventName, GenericEvent $event)
    {
        $this->jobParams = array($text, $eventName, $event->getAll());
        return $this;
    }

    /**
     * Execute job
     *
     * @param string       $text
     * @param string       $eventName
     * @param array        $eventData
     */
    public function execute($text, $eventName, array $eventData)
    {
        $event = new GenericEvent($eventData);
        $users = $this->getMentionedUsers($text);

        foreach ($users as $user) {
            if ($this->projectPermissionModel->isMember($event->getProjectId(), $user['id'])) {
                $event['mention'] = $user;
                $this->dispatcher->dispatch($eventName, $event);
            }
        }
    }

    /**
     * Get list of mentioned users
     *
     * @access public
     * @param  string $text
     * @return array
     */
    public function getMentionedUsers($text)
    {
        $users = array();

        if (preg_match_all('/@([^\s,!:?]+)/', $text, $matches)) {
            array_walk($matches[1], function (&$username) { $username = rtrim($username, '.'); });
            $users = $this->db->table(UserModel::TABLE)
                ->columns('id', 'username', 'name', 'email', 'language')
                ->eq('notifications_enabled', 1)
                ->neq('id', $this->userSession->getId())
                ->in('username', array_unique($matches[1]))
                ->findAll();
        }

        return $users;
    }
}
