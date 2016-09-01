<?php

namespace Kanboard\Controller;

/**
 * Export Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class ExportController extends BaseController
{
    /**
     * Common export method
     *
     * @access private
     * @param  string $model
     * @param  string $method
     * @param  string $filename
     * @param  string $action
     * @param  string $page_title
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    private function common($model, $method, $filename, $action, $page_title)
    {
        $project = $this->getProject();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            $data = $this->$model->$method($project['id'], $from, $to);
            $this->response->withFileDownload($filename.'.csv');
            $this->response->csv($data);
        } else {

            $this->response->html($this->helper->layout->project('export/'.$action, array(
                'values' => array(
                    'controller' => 'ExportController',
                    'action' => $action,
                    'project_id' => $project['id'],
                    'from' => $from,
                    'to' => $to,
                ),
                'errors' => array(),
                'project' => $project,
                'title' => $page_title,
            ), 'export/sidebar'));
        }
    }

    /**
     * Task export
     *
     * @access public
     */
    public function tasks()
    {
        $this->common('taskExport', 'export', t('Tasks'), 'tasks', t('Tasks Export'));
    }

    /**
     * Subtask export
     *
     * @access public
     */
    public function subtasks()
    {
        $this->common('subtaskExport', 'export', t('Subtasks'), 'subtasks', t('Subtasks Export'));
    }

    /**
     * Daily project summary export
     *
     * @access public
     */
    public function summary()
    {
        $this->common('projectDailyColumnStatsModel', 'getAggregatedMetrics', t('Summary'), 'summary', t('Daily project summary export'));
    }

    /**
     * Transition export
     *
     * @access public
     */
    public function transitions()
    {
        $this->common('transitionExport', 'export', t('Transitions'), 'transitions', t('Task transitions export'));
    }
}
