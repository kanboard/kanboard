<?php

namespace Kanboard\Api;

Use \Kanboard\Core\Base

/**
 * Subtask Time Tracking  API controller
 *
 * @package  api
 * @author   Nikolaos Georgakis
 */
class SubtaskTimeTrackingApi extends Base
{
    public function hasTimer($subtask_id,$user_id)
    {
        return $this->subtaskTimeTracking->hasTimer($subtask_id,$user_id);
    }

    public function logStartTime($subtask_id,$user_id)
    {
        return $this->subtaskTimeTracking->logStartTime($subtask_id,$user_id);
    }

    public function logEndTime($subtask_id,$user_id)
    {
        return $this->subtaskTimeTracking->logEndTime($subtask_id,$user_id);
    }

    public function getTimeSpent($subtask_id,$user_id)
    {
        return $this->subtaskTimeTracking->getTimeSpent($subtask_id,$user_id);
    }
}
