<?php

namespace Kanboard\Formatter;

use DateTimeImmutable;
use DateTimeZone;
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
     * Timezone of the dates, the current timezone by default
     *
     * @access protected
     * @var DateTimeZone|null
     */
    protected $timezone = null;

    /**
     * Render the task descriptions for readers without account
     *
     * @access protected
     * @var bool
     */
    protected $isPublicLink = false;

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
     * Set the timezone used to tell if a date has a time
     *
     * A feed is fetched without session, the current timezone is not the one of its owner.
     *
     * @access public
     * @param  DateTimeZone $timezone
     * @return $this
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Render the task descriptions like the public task view: no user mentions, links with the project token
     *
     * @access public
     * @param  bool $isPublicLink
     * @return $this
     */
    public function withPublicLinks($isPublicLink = true)
    {
        $this->isPublicLink = $isPublicLink;
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
        $now = $this->toDateTime(time());

        foreach ($query->findAll() as $task) {
            $start = $this->toDateTime($task[$startColumn]);
            $event = $this->getTaskIcalEvent($task, 'task-#'.$task['id'].'-'.$startColumn.'-'.$endColumn)
                ->setStart($start);

            if (! empty($task[$endColumn])) {
                $end = $this->toDateTime($task[$endColumn]);
                $event->setEnd($end)->setAllDay($this->isDateOnly($start) && $this->isDateOnly($end));
            } elseif ($start < $now) {
                // A started task without due date is still running
                $event->setEnd($now);
            } else {
                $event->setAllDay($this->isDateOnly($start));
            }

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
                ->setAllDay($this->isDateOnly($date));

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
            ->setDescriptionHtml($this->helper->text->markdown($task['description'], $this->isPublicLink))
            ->setUrl($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task['id']), '', true));

        return $component;
    }

    /**
     * Convert a timestamp to a date in the timezone of the feed
     *
     * @access private
     * @param  int|string $timestamp
     * @return DateTimeImmutable
     */
    private function toDateTime($timestamp)
    {
        return (new DateTimeImmutable('now', $this->timezone))->setTimestamp((int) $timestamp);
    }

    /**
     * A date at midnight has no time, like the due dates shown on the board
     *
     * @access private
     * @param  DateTimeImmutable $date
     * @return bool
     */
    private function isDateOnly(DateTimeImmutable $date)
    {
        return $date->format('Hi') === '0000';
    }
}
