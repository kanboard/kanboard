<?php

namespace Kanboard\Controller;

use Kanboard\Core\Csv;

/**
 * Task Import controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class TaskImportController extends BaseController
{
    /**
     * Upload the file and ask settings
     *
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\PageNotFoundException
     */
    public function show(array $values = array(), array $errors = array())
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('task_import/show', array(
            'project' => $project,
            'values' => $values,
            'errors' => $errors,
            'max_size' => get_upload_max_size(),
            'delimiters' => Csv::getDelimiters(),
            'enclosures' => Csv::getEnclosures(),
        )));
    }

    /**
     * Process CSV file
     */
    public function save()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        $filename = $this->request->getFilePath('file');

        if (! file_exists($filename)) {
            $this->show($values, array('file' => array(t('Unable to read your file'))));
        } else {
            $this->taskImport->projectId = $project['id'];

            $csv = new Csv($values['delimiter'], $values['enclosure']);
            $csv->setColumnMapping($this->taskImport->getColumnMapping());
            $csv->read($filename, array($this->taskImport, 'import'));

            if ($this->taskImport->counter > 0) {
                $this->flash->success(t('%d task(s) have been imported successfully.', $this->taskImport->counter));
            } else {
                $this->flash->failure(t('Nothing have been imported!'));
            }

            $this->response->redirect($this->helper->url->to('TaskImportController', 'show', array('project_id' => $project['id'])), true);
        }
    }

    /**
     * Generate template
     *
     */
    public function template()
    {
        $this->response->withFileDownload('tasks.csv');
        $this->response->csv(array($this->taskImport->getColumnMapping()));
    }
}
