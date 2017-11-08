<?php

namespace Kanboard\Formatter;

use DateTime;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Eluceo\iCal\Property\Event\Attendees;
use Eluceo\iCal\Property\Event\Organizer;
use Kanboard\Core\Filter\FormatterInterface;
use PicoDb\Table;

/**
 * iCal event formatter for tasks
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class TaskICalFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Calendar object
     *
     * @access protected
     * @var \Eluceo\iCal\Component\Calendar
     */
    protected $vCalendar;

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
     * @return $this
     */
    public function setCalendar(Calendar $vCalendar)
    {
        $this->vCalendar = $vCalendar;
        return $this;
    }

    /**
     * Transform results to iCal events
     *
     * @access public
     * @param  Table  $query
     * @param  string $startColumn
     * @param  string $endColumn
     * @return $this
     */
    public function addTasksWithStartAndDueDate(Table $query, $startColumn, $endColumn)
    {
        foreach ($query->findAll() as $task) {
            $start = new DateTime;
            $start->setTimestamp($task[$startColumn]);

            $end = new DateTime;
            $end->setTimestamp($task[$endColumn] ?: time());

            $vEvent = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-'.$startColumn.'-'.$endColumn);
            $vEvent->setDtStart($start);
            $vEvent->setDtEnd($end);

            $this->vCalendar->addComponent($vEvent);
        }

        return $this;
    }

    /**
     * Transform results to all day iCal events
     *
     * @access public
     * @param  Table $query
     * @return $this
     */
    public function addTasksWithDueDateOnly(Table $query)
    {
        foreach ($query->findAll() as $task) {
            $date = new DateTime;
            $date->setTimestamp($task['date_due']);

            $vEvent = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-date_due');
            $vEvent->setDtStart($date);
            $vEvent->setDtEnd($date);

            if ($date->format('Hi') === '0000') {
                $vEvent->setNoTime(true);
            }

            $this->vCalendar->addComponent($vEvent);
        }

        return $this;
    }

    /**
     * Get common events for task iCal events
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
        $vEvent->setDescription($task['description']);
        $vEvent->setDescriptionHTML($this->helper->text->markdown($task['description']));
        $vEvent->setUrl($this->helper->url->base().$this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));

        if (! empty($task['owner_id'])) {
            $attendees = new Attendees;
            $attendees->add(
                'MAILTO:'.($task['assignee_email'] ?: $task['assignee_username'].'@kanboard.local'),
                array('CN' => $task['assignee_name'] ?: $task['assignee_username'])
            );
            $vEvent->setAttendees($attendees);
        }

        if (! empty($task['creator_id'])) {
            $vEvent->setOrganizer(new Organizer(
                'MAILTO:' . $task['creator_email'] ?: $task['creator_username'].'@kanboard.local',
                array('CN' => $task['creator_name'] ?: $task['creator_username'])
            ));
        }

        return $vEvent;
    }
}
