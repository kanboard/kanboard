<?php

namespace Kanboard\Core\Event;

use Kanboard\Integration\GitlabWebhook;
use Kanboard\Integration\GithubWebhook;
use Kanboard\Integration\BitbucketWebhook;
use Kanboard\Model\Task;
use Kanboard\Model\TaskLink;

/**
 * Event Manager
 *
 * @package  event
 * @author   Frederic Guillot
 */
class EventManager
{
    /**
     * Extended events
     *
     * @access private
     * @var array
     */
    private $events = array();

    /**
     * Add new event
     *
     * @access public
     * @param  string  $event
     * @param  string  $description
     * @return EventManager
     */
    public function register($event, $description)
    {
        $this->events[$event] = $description;
        return $this;
    }

    /**
     * Get the list of events and description that can be used from the user interface
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        $events = array(
            TaskLink::EVENT_CREATE_UPDATE => t('Task link creation or modification'),
            Task::EVENT_MOVE_COLUMN => t('Move a task to another column'),
            Task::EVENT_UPDATE => t('Task modification'),
            Task::EVENT_CREATE => t('Task creation'),
            Task::EVENT_OPEN => t('Reopen a task'),
            Task::EVENT_CLOSE => t('Closing a task'),
            Task::EVENT_CREATE_UPDATE => t('Task creation or modification'),
            Task::EVENT_ASSIGNEE_CHANGE => t('Task assignee change'),
            GithubWebhook::EVENT_COMMIT => t('Github commit received'),
            GithubWebhook::EVENT_ISSUE_OPENED => t('Github issue opened'),
            GithubWebhook::EVENT_ISSUE_CLOSED => t('Github issue closed'),
            GithubWebhook::EVENT_ISSUE_REOPENED => t('Github issue reopened'),
            GithubWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE => t('Github issue assignee change'),
            GithubWebhook::EVENT_ISSUE_LABEL_CHANGE => t('Github issue label change'),
            GithubWebhook::EVENT_ISSUE_COMMENT => t('Github issue comment created'),
            GitlabWebhook::EVENT_COMMIT => t('Gitlab commit received'),
            GitlabWebhook::EVENT_ISSUE_OPENED => t('Gitlab issue opened'),
            GitlabWebhook::EVENT_ISSUE_REOPENED => t('Gitlab issue reopened'),
            GitlabWebhook::EVENT_ISSUE_CLOSED => t('Gitlab issue closed'),
            GitlabWebhook::EVENT_ISSUE_COMMENT => t('Gitlab issue comment created'),
            BitbucketWebhook::EVENT_COMMIT => t('Bitbucket commit received'),
            BitbucketWebhook::EVENT_ISSUE_OPENED => t('Bitbucket issue opened'),
            BitbucketWebhook::EVENT_ISSUE_CLOSED => t('Bitbucket issue closed'),
            BitbucketWebhook::EVENT_ISSUE_REOPENED => t('Bitbucket issue reopened'),
            BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE => t('Bitbucket issue assignee change'),
            BitbucketWebhook::EVENT_ISSUE_COMMENT => t('Bitbucket issue comment created'),
        );

        $events = array_merge($events, $this->events);
        asort($events);

        return $events;
    }
}
