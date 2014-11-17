<?php

namespace Event;

/**
 * Project daily summary listener
 *
 * @package event
 * @author  Frederic Guillot
 */
class ProjectDailySummaryListener extends Base
{
    /**
     * Execute the action
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function execute(array $data)
    {
        if (isset($data['project_id'])) {
            return $this->projectDailySummary->updateTotals($data['project_id'], date('Y-m-d'));
        }

        return false;
    }
}
