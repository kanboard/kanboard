<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Core\Security\Role;

class ProjectActivityEventFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Application role used to filter restricted comments
     *
     * @access private
     * @var string|null
     */
    private $role;

    /**
     * Set the role used to decide which comments are visible
     *
     * The activity stream is not always displayed in the context of the
     * current session (API token, public RSS feed), so the caller must be
     * able to state whose permissions apply.
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
     * Get the role used to filter comments
     *
     * Defaults to the role of the current session when not set explicitly.
     *
     * @access private
     * @return string
     */
    private function getAccessRole()
    {
        if ($this->role !== null) {
            return (string) $this->role;
        }

        return (string) $this->userSession->getRole();
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
        $role = $this->getAccessRole();

        foreach ($events as &$event) {
            $eventData = $this->unserializeEvent($event['data']);
            if (empty($eventData)) {
                continue;
            }

            $event += $eventData;
            unset($event['data']);

            if (isset($event['comment']) && ! $this->isCommentVisible($event['comment'], $role)) {
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
     * Check that the given role is allowed to see a comment
     *
     * Only known visibility values are accepted: anything unexpected is
     * treated as restricted so that a new value never leaks by default.
     *
     * @access protected
     * @param  array  $comment
     * @param  string $role
     * @return boolean
     */
    protected function isCommentVisible(array $comment, $role)
    {
        if (! isset($comment['visibility'])) {
            return false;
        }

        $visibility = $comment['visibility'];

        if (! is_string($visibility) || ! in_array($visibility, array(Role::APP_USER, Role::APP_MANAGER, Role::APP_ADMIN), true)) {
            return false;
        }

        if ($visibility === Role::APP_USER) {
            return true;
        }

        if ($role === Role::APP_ADMIN) {
            return true;
        }

        return $role === Role::APP_MANAGER && $visibility === Role::APP_MANAGER;
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
