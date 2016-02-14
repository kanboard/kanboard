<?php

namespace Kanboard\Controller;

use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * File File Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class TaskFile extends Base
{
    /**
     * Screenshot
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        if ($this->request->isPost() && $this->taskFile->uploadScreenshot($task['id'], $this->request->getValue('screenshot')) !== false) {
            $this->flash->success(t('Screenshot uploaded successfully.'));
            return $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), true);
        }

        $this->response->html($this->helper->layout->task('task_file/screenshot', array(
            'task' => $task,
        )));
    }

    /**
     * File upload form
     *
     * @access public
     */
    public function create()
    {
        $task = $this->getTask();

        $this->response->html($this->helper->layout->task('task_file/new', array(
            'task' => $task,
            'max_size' => $this->helper->text->phpToBytes(ini_get('upload_max_filesize')),
        )));
    }

    /**
     * File upload (save files)
     *
     * @access public
     */
    public function save()
    {
        $task = $this->getTask();

        if (! $this->taskFile->uploadFiles($task['id'], $this->request->getFileInfo('files'))) {
            $this->flash->failure(t('Unable to upload the file.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])), true);
    }

    /**
     * File download
     *
     * @access public
     */
    public function download()
    {
        try {
            $task = $this->getTask();
            $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

            if ($file['task_id'] != $task['id']) {
                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
            }

            $this->response->forceDownload($file['name']);
            $this->objectStorage->output($file['path']);
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Open a file (show the content in a popover)
     *
     * @access public
     */
    public function open()
    {
        $task = $this->getTask();
        $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id']) {
            $this->response->html($this->template->render('task_file/open', array(
                'file' => $file,
                'task' => $task,
            )));
        }
    }

    /**
     * Display image
     *
     * @access public
     */
    public function image()
    {
        try {
            $task = $this->getTask();
            $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

            if ($file['task_id'] == $task['id']) {
                $this->response->contentType($this->taskFile->getImageMimeType($file['name']));
                $this->objectStorage->output($file['path']);
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Display image thumbnails
     *
     * @access public
     */
    public function thumbnail()
    {
        $this->response->contentType('image/jpeg');

        try {
            $task = $this->getTask();
            $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

            if ($file['task_id'] == $task['id']) {
                $this->objectStorage->output($this->taskFile->getThumbnailPath($file['path']));
            }
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());

            // Try to generate thumbnail on the fly for images uploaded before Kanboard < 1.0.19
            $data = $this->objectStorage->get($file['path']);
            $this->taskFile->generateThumbnailFromData($file['path'], $data);
            $this->objectStorage->output($this->taskFile->getThumbnailPath($file['path']));
        }
    }

    /**
     * Remove a file
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $task = $this->getTask();
        $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id'] && $this->taskFile->remove($file['id'])) {
            $this->flash->success(t('File removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this file.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
    }

    /**
     * Confirmation dialog before removing a file
     *
     * @access public
     */
    public function confirm()
    {
        $task = $this->getTask();
        $file = $this->taskFile->getById($this->request->getIntegerParam('file_id'));

        $this->response->html($this->helper->layout->task('task_file/remove', array(
            'task' => $task,
            'file' => $file,
        )));
    }
}
