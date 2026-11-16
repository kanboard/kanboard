<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Core\Security\Role;

class ProjectActivityEventFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Application role used to filter comments, the role of the current user when null
     *
     * @access protected
     * @var string|null
     */
    protected $role = null;

    /**
     * Set the application role used to filter comments
     *
     * @access public
     * @param  string|null $role
     * @return $this
     */
    public function withRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $events = $this->query->findAll();
        $res = array();

        foreach ($events as &$event) {
            $eventData = $this->unserializeEvent($event['data']);
            if (empty($eventData)) {
                continue;
            }

            $event += $eventData;
            unset($event['data']);

            if (isset($event['comment']) && ! $this->canSeeComment($event['comment'])) {
                continue;
            }

            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->notificationModel->getTitleWithAuthor($event['author'], $event['event_name'], $event);
            $event['event_content'] = $this->renderEvent($event);
            $res[] = $event;
        }

        return $res;
    }

    /**
     * Return true if the application role allows to see the comment
     *
     * Without a role and a logged-in user, only comments visible to standard users are shown.
     *
     * @access protected
     * @param  array $comment
     * @return bool
     */
    protected function canSeeComment(array $comment)
    {
        $visibility = isset($comment['visibility']) ? $comment['visibility'] : Role::APP_USER;
        $role = $this->role !== null ? $this->role : $this->userSession->getRole();

        switch ($visibility) {
            case Role::APP_USER:
                return true;
            case Role::APP_MANAGER:
                return $role === Role::APP_MANAGER || $role === Role::APP_ADMIN;
            default:
                return $role === Role::APP_ADMIN;
        }
    }

    /**
     * Decode event data, supports unserialize() and json_decode()
     *
     * @access protected
     * @param  string   $data   Serialized data
     * @return array
     */
    protected function unserializeEvent($data)
    {
        // Ignore legacy events serialized with PHP due potential security issues.
        if ($data[0] === 'a') {
            return [];
        }

        return json_decode($data, true) ?: [];
    }

    /**
     * Get the event html content
     *
     * @access protected
     * @param  array     $params    Event properties
     * @return string
     */
    protected function renderEvent(array $params)
    {
        return $this->template->render(
            'event/'.str_replace('.', '_', $params['event_name']),
            $params
        );
    }
}
