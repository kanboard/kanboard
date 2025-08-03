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

            if (isset($event['comment'])) {
                if ($this->userSession->getRole() === Role::APP_USER && $event['comment']['visibility'] !== Role::APP_USER) {
                    continue;
                }
                if ($this->userSession->getRole() === Role::APP_MANAGER && $event['comment']['visibility'] === Role::APP_ADMIN) {
                    continue;
                }
            }

            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->notificationModel->getTitleWithAuthor($event['author'], $event['event_name'], $event);
            $event['event_content'] = $this->renderEvent($event);
            $res[] = $event;
        }

        return $res;
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
