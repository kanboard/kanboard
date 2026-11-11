<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Core\Security\Role;

class ProjectActivityEventFormatter extends BaseFormatter implements FormatterInterface
{
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

            if (isset($event['comment']) && ! $this->isCommentVisible($event['comment'])) {
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
     * Check that the current user is allowed to see the given comment
     *
     * Comments are filtered by visibility, so a role that is not explicitly
     * known to be privileged enough must never see a restricted comment.
     *
     * @access protected
     * @param  array  $comment
     * @return boolean
     */
    protected function isCommentVisible(array $comment)
    {
        $visibility = isset($comment['visibility']) ? $comment['visibility'] : Role::APP_USER;

        if ($visibility === Role::APP_USER) {
            return true;
        }

        $role = $this->userSession->getRole();

        if ($role === Role::APP_ADMIN) {
            return true;
        }

        // Project roles (and token-authenticated/public requests, which have no
        // application role) are not allowed to read comments restricted to managers.
        return $role === Role::APP_MANAGER && $visibility !== Role::APP_ADMIN;
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
