<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;

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
        return $this->subtaskTimeTrackingModel->hasTimer($subtask_id,$user_id);
    }

    public function logStartTime($subtask_id,$user_id)
    {
        return $this->subtaskTimeTrackingModel->logStartTime($subtask_id,$user_id);
    }

    public function logEndTime($subtask_id,$user_id)
    {
        return $this->subtaskTimeTrackingModel->logEndTime($subtask_id,$user_id);
    }

    public function getTimeSpent($subtask_id,$user_id)
    {
        return $this->subtaskTimeTrackingModel->getTimeSpent($subtask_id,$user_id);
    }
}
