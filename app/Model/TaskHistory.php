<?php

namespace Model;

use PDO;
use Core\Registry;
use Event\TaskHistoryListener;

/**
 * Task history model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskHistory extends BaseHistory
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'task_has_events';

    /**
     * Maximum number of events
     *
     * @var integer
     */
    const MAX_EVENTS = 5000;

    /**
     * Constructor
     *
     * @access public
     * @param  \Core\Registry  $registry  Registry instance
     */
    public function __construct(Registry $registry)
    {
        parent::__construct($registry);
        $this->table = self::TABLE;
    }

    /**
     * Create a new event
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $task_id       Task id
     * @param  integer   $creator_id    Author of the event (user id)
     * @param  string    $event_name    Task event name
     * @return boolean
     */
    public function create($project_id, $task_id, $creator_id, $event_name)
    {
        $values = array(
            'project_id' => $project_id,
            'task_id' => $task_id,
            'creator_id' => $creator_id,
            'event_name' => $event_name,
            'date_creation' => time(),
        );

        $this->db->startTransaction();

        $this->cleanup(self::MAX_EVENTS - 1);
        $result = $this->db->table(self::TABLE)->insert($values);

        $this->db->closeTransaction();

        return $result;
    }

    /**
     * Get all necessary content to display activity feed
     *
     * $author_name
     * $author_username
     * $task['id', 'title', 'position', 'column_name']
     */
    public function getAllContentByProjectId($project_id, $limit = 50)
    {
        $sql = '
            SELECT
                task_has_events.id,
                task_has_events.date_creation,
                task_has_events.event_name,
                task_has_events.task_id,
                tasks.title as task_title,
                tasks.position as task_position,
                columns.title as task_column_name,
                users.username as author_username,
                users.name as author_name
            FROM task_has_events
            LEFT JOIN users ON users.id=task_has_events.creator_id
            LEFT JOIN tasks ON tasks.id=task_has_events.task_id
            LEFT JOIN columns ON columns.id=tasks.column_id
            WHERE task_has_events.project_id = ?
            ORDER BY task_has_events.id DESC
            LIMIT '.$limit.' OFFSET 0
        ';

        $rq = $this->db->execute($sql, array($project_id));
        $events = $rq->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as &$event) {
            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->getTitle($event);
            $event['event_content'] = $this->getContent($event);
            $event['event_type'] = 'task';
        }

        return $events;
    }

    /**
     * Get the event title (translated)
     *
     * @access public
     * @param  array     $event    Event properties
     * @return string
     */
    public function getTitle(array $event)
    {
        $titles = array(
            Task::EVENT_UPDATE => t('%s updated the task #%d', $event['author'], $event['task_id']),
            Task::EVENT_CREATE => t('%s created the task #%d', $event['author'], $event['task_id']),
            Task::EVENT_CLOSE => t('%s closed the task #%d', $event['author'], $event['task_id']),
            Task::EVENT_OPEN => t('%s open the task #%d', $event['author'], $event['task_id']),
            Task::EVENT_MOVE_COLUMN => t('%s moved the task #%d to the column "%s"', $event['author'], $event['task_id'], $event['task_column_name']),
            Task::EVENT_MOVE_POSITION => t('%s moved the task #%d to the position %d in the column "%s"', $event['author'], $event['task_id'], $event['task_position'], $event['task_column_name']),
        );

        return isset($titles[$event['event_name']]) ? $titles[$event['event_name']] : '';
    }

    /**
     * Attach events to be able to record the history
     *
     * @access public
     */
    public function attachEvents()
    {
        $events = array(
            Task::EVENT_UPDATE,
            Task::EVENT_CREATE,
            Task::EVENT_CLOSE,
            Task::EVENT_OPEN,
            Task::EVENT_MOVE_COLUMN,
            Task::EVENT_MOVE_POSITION,
        );

        $listener = new TaskHistoryListener($this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }
}
