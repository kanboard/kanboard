<?php

namespace Controller;

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

        if ($this->request->isPost() && $this->file->uploadScreenshot($task['project_id'], $task['id'], $this->request->getValue('screenshot'))) {

            $this->session->flash(t('Screenshot uploaded successfully.'));

            if ($this->request->getStringParam('redirect') === 'board') {
                $this->response->redirect($this->helper->url('board', 'show', array('project_id' => $task['project_id'])));
            }

            $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
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

        if (! $this->file->upload($task['project_id'], $task['id'], 'files')) {
            $this->session->flashError(t('Unable to upload the file.'));
        }

        $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
    }

    /**
     * File download
     *
     * @access public
     */
    public function download()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));
        $filename = FILES_DIR.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {
            $this->response->forceDownload($file['name']);
            $this->response->binary(file_get_contents($filename));
        }

        $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
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
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));
        $filename = FILES_DIR.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {
            $metadata = getimagesize($filename);

            if (isset($metadata['mime'])) {
                $this->response->contentType($metadata['mime']);
                readfile($filename);
            }
        }
    }

    /**
     * Return image thumbnails
     *
     * @access public
     */
    public function thumbnail()
    {
        $task = $this->getTask();
        $file = $this->file->getById($this->request->getIntegerParam('file_id'));
        $filename = FILES_DIR.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {

            $this->response->contentType('image/jpeg');
            $this->file->generateThumbnail(
                $filename,
                $this->request->getIntegerParam('width'),
                $this->request->getIntegerParam('height')
            );
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

        $this->response->redirect($this->helper->url('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])));
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
