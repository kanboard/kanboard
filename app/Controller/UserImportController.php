<?php

namespace Kanboard\Controller;

use Kanboard\Core\Csv;
use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * User Import controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class UserImportController extends BaseController
{
    /**
     * Upload the file and ask settings
     *
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('user_import/show', array(
            'values' => $values,
            'errors' => $errors,
            'max_size' => get_upload_max_size(),
            'delimiters' => Csv::getDelimiters(),
            'enclosures' => Csv::getEnclosures(),
        )));
    }

    /**
     * Submit form
     */
    public function save()
    {
        $values = $this->request->getValues();

        // Note: $values is empty when the CSRF token is invalid.
        if (empty($values)) {
            throw new AccessForbiddenException();
        }

        $filename = $this->request->getFilePath('file');

        if (! file_exists($filename)) {
            $this->flash->failure(t('Unable to read your file'));
        } else {
            $this->importFile($values, $filename);
        }

        $this->response->redirect($this->helper->url->to('UserListController', 'show'));
    }

    /**
     * Generate template
     *
     */
    public function template()
    {
        $this->response->withFileDownload('users.csv');
        $this->response->csv(array($this->userImport->getColumnMapping()));
    }

    /**
     * Process file
     *
     * @param array $values
     * @param       $filename
     */
    private function importFile(array $values, $filename)
    {
        $csv = new Csv($values['delimiter'], $values['enclosure']);
        $csv->setColumnMapping($this->userImport->getColumnMapping());
        $csv->read($filename, array($this->userImport, 'import'));

        if ($this->userImport->counter > 0) {
            $this->flash->success(t('%d user(s) have been imported successfully.', $this->userImport->counter));
        } else {
            $this->flash->failure(t('Nothing has been imported!'));
        }
    }
}
