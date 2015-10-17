<?php

namespace Kanboard\Formatter;

use DateTime;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Eluceo\iCal\Property\Event\Attendees;

/**
 * iCal event formatter for task filter
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class TaskFilterICalendarFormatter extends TaskFilterCalendarEvent implements FormatterInterface
{
    /**
     * Calendar object
     *
     * @access private
     * @var \Eluceo\iCal\Component\Calendar
     */
    private $vCalendar;

    /**
     * Get Ical events
     *
     * @access public
     * @return string
     */
    public function format()
    {
        return $this->vCalendar->render();
    }

    /**
     * Set calendar object
     *
     * @access public
     * @param \Eluceo\iCal\Component\Calendar $vCalendar
     * @return TaskFilterICalendarFormatter
     */
    public function setCalendar(Calendar $vCalendar)
    {
        $this->vCalendar = $vCalendar;
        return $this;
    }

    /**
     * Transform results to ical events
     *
     * @access public
     * @return TaskFilterICalendarFormatter
     */
    public function addDateTimeEvents()
    {
        foreach ($this->query->findAll() as $task) {
            $start = new DateTime;
            $start->setTimestamp($task[$this->startColumn]);

            $end = new DateTime;
            $end->setTimestamp($task[$this->endColumn] ?: time());

            $vEvent = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-'.$this->startColumn.'-'.$this->endColumn);
            $vEvent->setDtStart($start);
            $vEvent->setDtEnd($end);

            $this->vCalendar->addComponent($vEvent);
        }

        return $this;
    }

    /**
     * Transform results to all day ical events
     *
     * @access public
     * @return TaskFilterICalendarFormatter
     */
    public function addFullDayEvents()
    {
        foreach ($this->query->findAll() as $task) {
            $date = new DateTime;
            $date->setTimestamp($task[$this->startColumn]);

            $vEvent = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-'.$this->startColumn);
            $vEvent->setDtStart($date);
            $vEvent->setDtEnd($date);
            $vEvent->setNoTime(true);

            $this->vCalendar->addComponent($vEvent);
        }

        return $this;
    }

    /**
     * Get common events for task ical events
     *
     * @access protected
     * @param  array   $task
     * @param  string  $uid
     * @return Event
     */
    protected function getTaskIcalEvent(array &$task, $uid)
    {
        $dateCreation = new DateTime;
        $dateCreation->setTimestamp($task['date_creation']);

        $dateModif = new DateTime;
        $dateModif->setTimestamp($task['date_modification']);

        $vEvent = new Event($uid);
        $vEvent->setCreated($dateCreation);
        $vEvent->setModified($dateModif);
        $vEvent->setUseTimezone(true);
        $vEvent->setSummary(t('#%d', $task['id']).' '.$task['title']);
        $vEvent->setUrl($this->helper->url->base().$this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));

        if (! empty($task['owner_id'])) {
            $vEvent->setOrganizer($task['assignee_name'] ?: $task['assignee_username'], $task['assignee_email']);
        }

        if (! empty($task['creator_id'])) {
            $attendees = new Attendees;
            $attendees->add('MAILTO:'.($task['creator_email'] ?: $task['creator_username'].'@kanboard.local'));
            $vEvent->setAttendees($attendees);
        }

        return $vEvent;
    }
}
