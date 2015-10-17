<?php

namespace Kanboard\Controller;

use Kanboard\Core\Csv;

/**
 * User Import controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class UserImport extends Base
{
    /**
     * Upload the file and ask settings
     *
     */
    public function step1(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->layout('user_import/step1', array(
            'values' => $values,
            'errors' => $errors,
            'max_size' => ini_get('upload_max_filesize'),
            'delimiters' => Csv::getDelimiters(),
            'enclosures' => Csv::getEnclosures(),
            'title' => t('Import users from CSV file'),
        )));
    }

    /**
     * Process CSV file
     *
     */
    public function step2()
    {
        $values = $this->request->getValues();
        $filename = $this->request->getFilePath('file');

        if (! file_exists($filename)) {
            $this->step1($values, array('file' => array(t('Unable to read your file'))));
        }

        $csv = new Csv($values['delimiter'], $values['enclosure']);
        $csv->setColumnMapping($this->userImport->getColumnMapping());
        $csv->read($filename, array($this->userImport, 'import'));

        if ($this->userImport->counter > 0) {
            $this->session->flash(t('%d user(s) have been imported successfully.', $this->userImport->counter));
        } else {
            $this->session->flashError(t('Nothing have been imported!'));
        }

        $this->response->redirect($this->helper->url->to('userImport', 'step1'));
    }

    /**
     * Generate template
     *
     */
    public function template()
    {
        $this->response->forceDownload('users.csv');
        $this->response->csv(array($this->userImport->getColumnMapping()));
    }
}
