<?php

namespace Kanboard\Model;

use DateInterval;
use DateTime;

/**
 * Task Recurrence
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskRecurrenceModel extends TaskDuplicationModel
{
    /**
     * Duplicate recurring task
     *
     * @access public
     * @param  integer             $task_id      Task id
     * @return boolean|integer                   Recurrence task id
     */
    public function duplicateRecurringTask($task_id)
    {
        $values = $this->copyFields($task_id);

        if ($values['recurrence_status'] == TaskModel::RECURRING_STATUS_PENDING) {
            $values['recurrence_parent'] = $task_id;
            $values['column_id'] = $this->columnModel->getFirstColumnId($values['project_id']);
            $this->calculateRecurringTaskDueDate($values);

            $recurring_task_id = $this->save($task_id, $values);

            if ($recurring_task_id > 0) {
                $parent_update = $this->db
                    ->table(TaskModel::TABLE)
                    ->eq('id', $task_id)
                    ->update(array(
                        'recurrence_status' => TaskModel::RECURRING_STATUS_PROCESSED,
                        'recurrence_child' => $recurring_task_id,
                    ));

                if ($parent_update) {
                    return $recurring_task_id;
                }
            }
        }

        return false;
    }

    /**
     * Calculate new due date for new recurrence task
     *
     * @access public
     * @param  array   $values   Task fields
     */
    public function calculateRecurringTaskDueDate(array &$values)
    {
        if (! empty($values['date_due']) && $values['recurrence_factor'] != 0) {
            if ($values['recurrence_basedate'] == TaskModel::RECURRING_BASEDATE_TRIGGERDATE) {
                $values['date_due'] = time();
            }

            $factor = abs($values['recurrence_factor']);
            $subtract = $values['recurrence_factor'] < 0;

            switch ($values['recurrence_timeframe']) {
                case TaskModel::RECURRING_TIMEFRAME_MONTHS:
                    $interval = 'P' . $factor . 'M';
                    break;
                case TaskModel::RECURRING_TIMEFRAME_YEARS:
                    $interval = 'P' . $factor . 'Y';
                    break;
                default:
                    $interval = 'P' . $factor . 'D';
            }

            $date_due = new DateTime();
            $date_due->setTimestamp($values['date_due']);

            $subtract ? $date_due->sub(new DateInterval($interval)) : $date_due->add(new DateInterval($interval));

            $values['date_due'] = $date_due->getTimestamp();
        }
    }
}
