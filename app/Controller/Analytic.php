<?php

namespace Controller;

/**
 * Project Anaytic controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Analytic extends Base
{
    /**
     * Common layout for analytic views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());
        $params['analytic_content_for_layout'] = $this->template->load($template, $params);

        return $this->template->layout('analytic/layout', $params);
    }

    /**
     * Show task distribution graph
     *
     * @access public
     */
    public function repartition()
    {
        $project = $this->getProject();
        $metrics = $this->projectAnalytic->getTaskRepartition($project['id']);

        if ($this->request->isAjax()) {
            $this->response->json(array(
                'metrics' => $metrics,
                'labels' => array(
                    'column_title' => t('Column'),
                    'nb_tasks' => t('Number of tasks'),
                )
            ));
        }
        else {
            $this->response->html($this->layout('analytic/repartition', array(
                'project' => $project,
                'metrics' => $metrics,
                'title' => t('Task repartition for "%s"', $project['name']),
            )));
        }
    }
}
