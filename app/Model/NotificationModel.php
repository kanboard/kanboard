<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Notification
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class NotificationModel extends Base
{
    /**
     * Get the event title with author
     *
     * @access public
     * @param  string  $event_author
     * @param  string  $event_name
     * @param  array   $event_data
     * @return string
     */
    public function getTitleWithAuthor($event_author, $event_name, array $event_data)
    {
        switch ($event_name) {
            case TaskModel::EVENT_ASSIGNEE_CHANGE:
                $assignee = $event_data['task']['assignee_name'] ?: $event_data['task']['assignee_username'];

                if (! empty($assignee)) {
                    return e('%s change the assignee of the task #%d to %s', $event_author, $event_data['task']['id'], $assignee);
                }

                return e('%s remove the assignee of the task %s', $event_author, e('#%d', $event_data['task']['id']));
            case TaskModel::EVENT_UPDATE:
                return e('%s updated the task #%d', $event_author, $event_data['task']['id']);
            case TaskModel::EVENT_CREATE:
                return e('%s created the task #%d', $event_author, $event_data['task']['id']);
            case TaskModel::EVENT_CLOSE:
                return e('%s closed the task #%d', $event_author, $event_data['task']['id']);
            case TaskModel::EVENT_OPEN:
                return e('%s open the task #%d', $event_author, $event_data['task']['id']);
            case TaskModel::EVENT_MOVE_COLUMN:
                return e(
                    '%s moved the task #%d to the column "%s"',
                    $event_author,
                    $event_data['task']['id'],
                    $event_data['task']['column_title']
                );
            case TaskModel::EVENT_MOVE_POSITION:
                return e(
                    '%s moved the task #%d to the position %d in the column "%s"',
                    $event_author,
                    $event_data['task']['id'],
                    $event_data['task']['position'],
                    $event_data['task']['column_title']
                );
            case TaskModel::EVENT_MOVE_SWIMLANE:
                if ($event_data['task']['swimlane_id'] == 0) {
                    return e('%s moved the task #%d to the first swimlane', $event_author, $event_data['task']['id']);
                }

                return e(
                    '%s moved the task #%d to the swimlane "%s"',
                    $event_author,
                    $event_data['task']['id'],
                    $event_data['task']['swimlane_name']
                );
            case SubtaskModel::EVENT_UPDATE:
                return e('%s updated a subtask for the task #%d', $event_author, $event_data['task']['id']);
            case SubtaskModel::EVENT_CREATE:
                return e('%s created a subtask for the task #%d', $event_author, $event_data['task']['id']);
            case CommentModel::EVENT_UPDATE:
                return e('%s updated a comment on the task #%d', $event_author, $event_data['task']['id']);
            case CommentModel::EVENT_CREATE:
                return e('%s commented on the task #%d', $event_author, $event_data['task']['id']);
            case TaskFileModel::EVENT_CREATE:
                return e('%s attached a file to the task #%d', $event_author, $event_data['task']['id']);
            case TaskModel::EVENT_USER_MENTION:
                return e('%s mentioned you in the task #%d', $event_author, $event_data['task']['id']);
            case CommentModel::EVENT_USER_MENTION:
                return e('%s mentioned you in a comment on the task #%d', $event_author, $event_data['task']['id']);
            default:
                return e('Notification');
        }
    }

    /**
     * Get the event title without author
     *
     * @access public
     * @param  string  $event_name
     * @param  array   $event_data
     * @return string
     */
    public function getTitleWithoutAuthor($event_name, array $event_data)
    {
        switch ($event_name) {
            case TaskFileModel::EVENT_CREATE:
                return e('New attachment on task #%d: %s', $event_data['file']['task_id'], $event_data['file']['name']);
            case CommentModel::EVENT_CREATE:
                return e('New comment on task #%d', $event_data['comment']['task_id']);
            case CommentModel::EVENT_UPDATE:
                return e('Comment updated on task #%d', $event_data['comment']['task_id']);
            case SubtaskModel::EVENT_CREATE:
                return e('New subtask on task #%d', $event_data['subtask']['task_id']);
            case SubtaskModel::EVENT_UPDATE:
                return e('Subtask updated on task #%d', $event_data['subtask']['task_id']);
            case TaskModel::EVENT_CREATE:
                return e('New task #%d: %s', $event_data['task']['id'], $event_data['task']['title']);
            case TaskModel::EVENT_UPDATE:
                return e('Task updated #%d', $event_data['task']['id']);
            case TaskModel::EVENT_CLOSE:
                return e('Task #%d closed', $event_data['task']['id']);
            case TaskModel::EVENT_OPEN:
                return e('Task #%d opened', $event_data['task']['id']);
            case TaskModel::EVENT_MOVE_COLUMN:
                return e('Column changed for task #%d', $event_data['task']['id']);
            case TaskModel::EVENT_MOVE_POSITION:
                return e('New position for task #%d', $event_data['task']['id']);
            case TaskModel::EVENT_MOVE_SWIMLANE:
                return e('Swimlane changed for task #%d', $event_data['task']['id']);
            case TaskModel::EVENT_ASSIGNEE_CHANGE:
                return e('Assignee changed on task #%d', $event_data['task']['id']);
            case TaskModel::EVENT_OVERDUE:
                $nb = count($event_data['tasks']);
                return $nb > 1 ? e('%d overdue tasks', $nb) : e('Task #%d is overdue', $event_data['tasks'][0]['id']);
            case TaskModel::EVENT_USER_MENTION:
                return e('You were mentioned in the task #%d', $event_data['task']['id']);
            case CommentModel::EVENT_USER_MENTION:
                return e('You were mentioned in a comment on the task #%d', $event_data['task']['id']);
            default:
                return e('Notification');
        }
    }

    /**
     * Get task id from event
     *
     * @access public
     * @param  string  $event_name
     * @param  array   $event_data
     * @return integer
     */
    public function getTaskIdFromEvent($event_name, array $event_data)
    {
        switch ($event_name) {
            case TaskFileModel::EVENT_CREATE:
                return $event_data['file']['task_id'];
            case CommentModel::EVENT_CREATE:
            case CommentModel::EVENT_UPDATE:
                return $event_data['comment']['task_id'];
            case SubtaskModel::EVENT_CREATE:
            case SubtaskModel::EVENT_UPDATE:
                return $event_data['subtask']['task_id'];
            case TaskModel::EVENT_CREATE:
            case TaskModel::EVENT_UPDATE:
            case TaskModel::EVENT_CLOSE:
            case TaskModel::EVENT_OPEN:
            case TaskModel::EVENT_MOVE_COLUMN:
            case TaskModel::EVENT_MOVE_POSITION:
            case TaskModel::EVENT_MOVE_SWIMLANE:
            case TaskModel::EVENT_ASSIGNEE_CHANGE:
            case CommentModel::EVENT_USER_MENTION:
            case TaskModel::EVENT_USER_MENTION:
                return $event_data['task']['id'];
            case TaskModel::EVENT_OVERDUE:
                return $event_data['tasks'][0]['id'];
            default:
                return 0;
        }
    }
}
