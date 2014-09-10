<?php

namespace Model;

use PDO;
use Core\Registry;
use Event\CommentHistoryListener;

/**
 * Comment history model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class CommentHistory extends BaseHistory
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'comment_has_events';

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
     * @param  integer   $comment_id    Comment id
     * @param  integer   $creator_id    Author of the event (user id)
     * @param  string    $event_name    Task event name
     * @param  string    $data          Current comment
     * @return boolean
     */
    public function create($project_id, $task_id, $comment_id, $creator_id, $event_name, $data)
    {
        $values = array(
            'project_id' => $project_id,
            'task_id' => $task_id,
            'comment_id' => $comment_id,
            'creator_id' => $creator_id,
            'event_name' => $event_name,
            'date_creation' => time(),
            'data' => $data,
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
                comment_has_events.id,
                comment_has_events.date_creation,
                comment_has_events.event_name,
                comment_has_events.data as comment,
                comment_has_events.task_id,
                tasks.title as task_title,
                users.username as author_username,
                users.name as author_name
            FROM comment_has_events
            LEFT JOIN users ON users.id=comment_has_events.creator_id
            LEFT JOIN tasks ON tasks.id=comment_has_events.task_id
            WHERE comment_has_events.project_id = ?
            ORDER BY comment_has_events.id DESC
            LIMIT '.$limit.' OFFSET 0
        ';

        $rq = $this->db->execute($sql, array($project_id));
        $events = $rq->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as &$event) {
            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->getTitle($event);
            $event['event_content'] = $this->getContent($event);
            $event['event_type'] = 'comment';
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
            Comment::EVENT_UPDATE => t('%s updated a comment on the task #%d', $event['author'], $event['task_id']),
            Comment::EVENT_CREATE => t('%s commented on the task #%d', $event['author'], $event['task_id']),
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
            Comment::EVENT_UPDATE,
            Comment::EVENT_CREATE,
        );

        $listener = new CommentHistoryListener($this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }
}
