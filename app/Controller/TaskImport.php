<?php

namespace Kanboard\Controller;

use Kanboard\Core\Csv;

/**
 * Task Import controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class TaskImport extends Base
{
    /**
     * Upload the file and ask settings
     *
     */
    public function step1(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->projectLayout('task_import/step1', array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'max_size' => ini_get('upload_max_filesize'),
            'delimiters' => Csv::getDelimiters(),
            'enclosures' => Csv::getEnclosures(),
            'title' => t('Import tasks from CSV file'),
        )));
    }

    /**
     * Process CSV file
     *
     */
    public function step2()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $filename = $this->request->getFilePath('file');

        if (! file_exists($filename)) {
            $this->step1($values, array('file' => array(t('Unable to read your file'))));
        }

        $this->taskImport->projectId = $project['id'];

        $csv = new Csv($values['delimiter'], $values['enclosure']);
        $csv->setColumnMapping($this->taskImport->getColumnMapping());
        $csv->read($filename, array($this->taskImport, 'import'));

        if ($this->taskImport->counter > 0) {
            $this->session->flash(t('%d task(s) have been imported successfully.', $this->taskImport->counter));
        } else {
            $this->session->flashError(t('Nothing have been imported!'));
        }

        $this->response->redirect($this->helper->url->to('taskImport', 'step1', array('project_id' => $project['id'])));
    }

    /**
     * Generate template
     *
     */
    public function template()
    {
        $this->response->forceDownload('tasks.csv');
        $this->response->csv(array($this->taskImport->getColumnMapping()));
    }
}
