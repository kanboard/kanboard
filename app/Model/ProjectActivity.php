<?php

namespace Model;

/**
 * Project activity model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class ProjectActivity extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_activities';

    /**
     * Maximum number of events
     *
     * @var integer
     */
    const MAX_EVENTS = 1000;

    /**
     * Add a new event for the project
     *
     * @access public
     * @param  integer     $project_id      Project id
     * @param  integer     $task_id         Task id
     * @param  integer     $creator_id      User id
     * @param  string      $event_name      Event name
     * @param  array       $data            Event data (will be serialized)
     * @return boolean
     */
    public function createEvent($project_id, $task_id, $creator_id, $event_name, array $data)
    {
        $values = array(
            'project_id' => $project_id,
            'task_id' => $task_id,
            'creator_id' => $creator_id,
            'event_name' => $event_name,
            'date_creation' => time(),
            'data' => json_encode($data),
        );

        $this->cleanup(self::MAX_EVENTS - 1);
        return $this->db->table(self::TABLE)->insert($values);
    }

    /**
     * Get all events for the given project
     *
     * @access public
     * @param  integer     $project_id      Project id
     * @param  integer     $limit           Maximum events number
     * @return array
     */
    public function getProject($project_id, $limit = 50)
    {
        return $this->getProjects(array($project_id), $limit);
    }

    /**
     * Get all events for the given projects list
     *
     * @access public
     * @param  integer[]   $project_ids     Projects id
     * @param  integer     $limit           Maximum events number
     * @return array
     */
    public function getProjects(array $project_ids, $limit = 50)
    {
        if (empty($project_ids)) {
            return array();
        }

        $events = $this->db->table(self::TABLE)
                           ->columns(
                                self::TABLE.'.*',
                                User::TABLE.'.username AS author_username',
                                User::TABLE.'.name AS author_name'
                           )
                           ->in('project_id', $project_ids)
                           ->join(User::TABLE, 'id', 'creator_id')
                           ->desc('id')
                           ->limit($limit)
                           ->findAll();

        foreach ($events as &$event) {

            $event += $this->decode($event['data']);
            unset($event['data']);

            $event['author'] = $event['author_name'] ?: $event['author_username'];
            $event['event_title'] = $this->getTitle($event);
            $event['event_content'] = $this->getContent($event);
        }

        return $events;
    }

    /**
     * Remove old event entries to avoid large table
     *
     * @access public
     * @param  integer    $max    Maximum number of items to keep in the table
     */
    public function cleanup($max)
    {
        if ($this->db->table(self::TABLE)->count() > $max) {

            $this->db->execute('
                DELETE FROM '.self::TABLE.'
                WHERE id <= (
                    SELECT id FROM (
                        SELECT id FROM '.self::TABLE.' ORDER BY id DESC LIMIT 1 OFFSET '.$max.'
                    ) foo
                )'
            );
        }
    }

    /**
     * Get the event html content
     *
     * @access public
     * @param  array     $params    Event properties
     * @return string
     */
    public function getContent(array $params)
    {
        return $this->template->render(
            'event/'.str_replace('.', '_', $params['event_name']),
            $params
        );
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
        switch ($event['event_name']) {
            case Task::EVENT_ASSIGNEE_CHANGE:
                return t('%s change the assignee of the task #%d to %s', $event['author'], $event['task']['id'], $event['task']['assignee_name'] ?: $event['task']['assignee_username']);
            case Task::EVENT_UPDATE:
                return t('%s updated the task #%d', $event['author'], $event['task']['id']);
            case Task::EVENT_CREATE:
                return t('%s created the task #%d', $event['author'], $event['task']['id']);
            case Task::EVENT_CLOSE:
                return t('%s closed the task #%d', $event['author'], $event['task']['id']);
            case Task::EVENT_OPEN:
                return t('%s open the task #%d', $event['author'], $event['task']['id']);
            case Task::EVENT_MOVE_COLUMN:
                return t('%s moved the task #%d to the column "%s"', $event['author'], $event['task']['id'], $event['task']['column_title']);
            case Task::EVENT_MOVE_POSITION:
                return t('%s moved the task #%d to the position %d in the column "%s"', $event['author'], $event['task']['id'], $event['task']['position'], $event['task']['column_title']);
            case SubTask::EVENT_UPDATE:
                return t('%s updated a subtask for the task #%d', $event['author'], $event['task']['id']);
            case SubTask::EVENT_CREATE:
                return t('%s created a subtask for the task #%d', $event['author'], $event['task']['id']);
            case Comment::EVENT_UPDATE:
                return t('%s updated a comment on the task #%d', $event['author'], $event['task']['id']);
            case Comment::EVENT_CREATE:
                return t('%s commented on the task #%d', $event['author'], $event['task']['id']);
            default:
                return '';
        }
    }

    /**
     * Decode event data, supports unserialize() and json_decode()
     *
     * @access public
     * @param  string   $data   Serialized data
     * @return array
     */
    public function decode($data)
    {
        if ($data{0} === 'a') {
            return unserialize($data);
        }

        return json_decode($data, true) ?: array();
    }
}
