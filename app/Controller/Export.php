<?php

namespace Controller;

/**
 * Export controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Export extends Base
{
    /**
     * Common export method
     *
     * @access private
     */
    private function common($model, $method, $filename, $action, $page_title)
    {
        $project = $this->getProject();
        $from = $this->request->getStringParam('from');
        $to = $this->request->getStringParam('to');

        if ($from && $to) {
            $data = $this->$model->$method($project['id'], $from, $to);
            $this->response->forceDownload($filename.'.csv');
            $this->response->csv($data);
        }

        $this->response->html($this->projectLayout('export/'.$action, array(
            'values' => array(
                'controller' => 'export',
                'action' => $action,
                'project_id' => $project['id'],
                'from' => $from,
                'to' => $to,
            ),
            'errors' => array(),
            'date_format' => $this->config->get('application_date_format'),
            'date_formats' => $this->dateParser->getAvailableFormats(),
            'project' => $project,
            'title' => $page_title,
        )));
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
        $this->common('projectDailySummary', 'getAggregatedMetrics', t('Summary'), 'summary', t('Daily project summary export'));
    }
}
