<?php

namespace Kanboard\Analytic;

use Kanboard\Core\Base;

/**
 * User Distribution
 *
 * @package  analytic
 * @author   Frederic Guillot
 */
class UserDistributionAnalytic extends Base
{
    /**
     * Build Report
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function build($project_id)
    {
        $metrics = array();
        $total = 0;
        $tasks = $this->taskFinderModel->getAll($project_id);
        $users = $this->projectUserRoleModel->getAssignableUsersList($project_id);

        foreach ($tasks as $task) {
            $user = isset($users[$task['owner_id']]) ? $users[$task['owner_id']] : $users[0];
            $total++;

            if (! isset($metrics[$user])) {
                $metrics[$user] = array(
                    'nb_tasks' => 0,
                    'percentage' => 0,
                    'user' => $user,
                );
            }

            $metrics[$user]['nb_tasks']++;
        }

        if ($total === 0) {
            return array();
        }

        foreach ($metrics as &$metric) {
            $metric['percentage'] = round(($metric['nb_tasks'] * 100) / $total, 2);
        }

        ksort($metrics);

        return array_values($metrics);
    }
}
