<?php

namespace Kanboard\Formatter;

use DateTimeImmutable;
use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Core\ICalendar\Calendar;
use Kanboard\Core\ICalendar\Component;
use Kanboard\Core\ICalendar\Event;
use Kanboard\Core\ICalendar\Todo;
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
     * @var Calendar
     */
    protected $calendar;

    /**
     * Get Ical events
     *
     * @access public
     * @return string
     */
    public function format()
    {
        return $this->calendar->render();
    }

    /**
     * Set calendar object
     *
     * @access public
     * @param  Calendar $calendar
     * @return $this
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
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
            $event = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-'.$startColumn.'-'.$endColumn)
                ->setStart($this->toDateTime($task[$startColumn]))
                ->setEnd($this->toDateTime($task[$endColumn] ?: time()));

            $this->calendar->addEvent($event);
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
            $date = $this->toDateTime($task['date_due']);

            $event = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-date_due')
                ->setStart($date)
                ->setEnd($date)
                ->setAllDay($date->format('Hi') === '0000');

            $this->calendar->addEvent($event);
        }

        return $this;
    }

    /**
     * Transform results to iCal to-dos
     *
     * @access public
     * @param  Table $query
     * @return $this
     */
    public function addTasksWithoutDates(Table $query)
    {
        foreach ($query->findAll() as $task) {
            $this->calendar->addTodo($this->getTaskIcalTodo($task, 'task-#'.$task['id'].'-todo'));
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
        return $this->setTaskProperties(new Event($uid), $task);
    }

    /**
     * Get iCal to-do for a task
     *
     * @access protected
     * @param  array   $task
     * @param  string  $uid
     * @return Todo
     */
    protected function getTaskIcalTodo(array &$task, $uid)
    {
        return $this->setTaskProperties(new Todo($uid), $task);
    }

    /**
     * Set the properties shared by all task components
     *
     * @access private
     * @template T of Component
     * @param  T     $component
     * @param  array $task
     * @return T
     */
    private function setTaskProperties(Component $component, array $task)
    {
        $component
            ->setCreated($this->toDateTime($task['date_creation']))
            ->setModified($this->toDateTime($task['date_modification']))
            ->setSummary(t('#%d', $task['id']).' '.$task['title'])
            ->setDescription((string) $task['description'])
            ->setDescriptionHtml($this->helper->text->markdown($task['description']))
            ->setUrl($this->helper->url->base().$this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id'])));

        if (! empty($task['owner_id'])) {
            $component->addAttendee(
                (string) ($task['assignee_email'] ?: $task['assignee_username'].'@kanboard.local'),
                (string) ($task['assignee_name'] ?: $task['assignee_username'])
            );
        }

        if (! empty($task['creator_id'])) {
            $component->setOrganizer(
                (string) ($task['creator_email'] ?: $task['creator_username'].'@kanboard.local'),
                (string) ($task['creator_name'] ?: $task['creator_username'])
            );
        }

        return $component;
    }

    /**
     * Convert a timestamp to a date in the current timezone
     *
     * @access private
     * @param  int|string $timestamp
     * @return DateTimeImmutable
     */
    private function toDateTime($timestamp)
    {
        return (new DateTimeImmutable())->setTimestamp((int) $timestamp);
    }
}
