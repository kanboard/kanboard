<?php

namespace Kanboard\Controller;

use Kanboard\Core\ObjectStorage\ObjectStorageException;

/**
 * File controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class File extends Base
{
    /**
     * Screenshot
     *
     * @access public
     */
    public function screenshot()
    {
        $task = $this->getTask();

        if ($this->request->isPost() && $this->file->uploadScreenshot($task['project_id'], $task['id'], $this->request->getValue('screenshot')) !== false) {
            $this->session->flash(t('Screenshot uploaded successfully.'));

            if ($this->request->getStringParam('redirect') === 'board') {
                $this->response->redirect($this->helper->url->to('board', 'show', array('project_id' => $task['project_id'])));
            }

            $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
        }

        $this->response->html($this->taskLayout('file/screenshot', array(
            'task' => $task,
            'redirect' => 'task',
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

        $this->response->html($this->taskLayout('file/new', array(
            'task' => $task,
            'max_size' => ini_get('upload_max_filesize'),
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

        if (! $this->file->uploadFiles($task['project_id'], $task['id'], 'files')) {
            $this->session->flashError(t('Unable to upload the file.'));
        }

        $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
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
            $file = $this->file->getById($this->request->getIntegerParam('file_id'));

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
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id']) {
            $this->response->html($this->template->render('file/open', array(
                'file' => $file,
                'task' => $task,
            )));
        }
    }

    /**
     * Return the file content (work only for images)
     *
     * @access public
     */
    public function image()
    {
        try {
            $task = $this->getTask();
            $file = $this->file->getById($this->request->getIntegerParam('file_id'));

            if ($file['task_id'] != $task['id']) {
                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
            }

            $this->response->contentType($this->file->getImageMimeType($file['name']));
            $this->objectStorage->output($file['path']);
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Return image thumbnails
     *
     * @access public
     */
    public function thumbnail()
    {
        try {
            $task = $this->getTask();
            $file = $this->file->getById($this->request->getIntegerParam('file_id'));

            if ($file['task_id'] != $task['id']) {
                $this->response->redirect($this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
            }

            $this->response->contentType('image/jpeg');
            $this->objectStorage->output($this->file->getThumbnailPath($file['path']));
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
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
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        if ($file['task_id'] == $task['id'] && $this->file->remove($file['id'])) {
            $this->session->flash(t('File removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this file.'));
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
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));

        $this->response->html($this->taskLayout('file/remove', array(
            'task' => $task,
            'file' => $file,
        )));
    }
}
