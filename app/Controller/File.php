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

        if ($this->file->upload($task['project_id'], $task['id'], 'files') === true) {
            $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id'].'#attachments');
        }
        else {
            $this->session->flashError(t('Unable to upload the file.'));
            $this->response->redirect('?controller=file&action=create&task_id='.$task['id'].'&project_id='.$task['project_id']);
        }
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

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id']);
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
        $width_param = $this->request->getIntegerParam('width');
        $height_param = $this->request->getIntegerParam('height');
        $filename = FILES_DIR.$file['path'];

        if ($file['task_id'] == $task['id'] && file_exists($filename)) {

            // Get new sizes
            list($width, $height) = getimagesize($filename);

            if ($width_param == 0 && $height_param == 0) {
                $newwidth = 100;
                $newheight = 100;
            } elseif ($width_param > 0 && $height_param == 0) {
                $newwidth = $width_param;
                $newheight = floor($height * ($width_param / $width));
            } elseif ($width_param == 0 && $height_param > 0) {
                $newwidth = floor($width * ($height_param / $height));
                $newheight = $height_param;
            } else {
                $newwidth = $width_param;
                $newheight = $height_param;
            }

            // Load
            $thumb = imagecreatetruecolor($newwidth, $newheight);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $source = imagecreatefromjpeg($filename);
                    break;
                case 'png':
                    $source = imagecreatefrompng($filename);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($filename);
                    break;
                default:
                    die('File "' . $filename . '" is not valid jpg, png or gif image.');
                    break;
            }

            // Resize
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            $metadata = getimagesize($filename);

            if (isset($metadata['mime'])) {
                $this->response->contentType($metadata['mime']);
                imagejpeg($thumb);
            }
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

        $this->response->redirect('?controller=task&action=show&task_id='.$task['id'].'&project_id='.$task['project_id']);
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
