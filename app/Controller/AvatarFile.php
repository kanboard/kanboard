<?php

namespace Kanboard\Controller;

use Kanboard\Core\ObjectStorage\ObjectStorageException;
use Kanboard\Core\Thumbnail;

/**
 * Avatar File Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class AvatarFile extends Base
{
    /**
     * Display avatar page
     */
    public function show()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->user('avatar_file/show', array(
            'user' => $user,
        )));
    }

    /**
     * Upload Avatar
     */
    public function upload()
    {
        $user = $this->getUser();

        if (! $this->avatarFile->uploadFile($user['id'], $this->request->getFileInfo('avatar'))) {
            $this->flash->failure(t('Unable to upload the file.'));
        }

        $this->response->redirect($this->helper->url->to('AvatarFile', 'show', array('user_id' => $user['id'])));
    }

    /**
     * Remove Avatar image
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();
        $this->avatarFile->remove($user['id']);
        $this->response->redirect($this->helper->url->to('AvatarFile', 'show', array('user_id' => $user['id'])));
    }

    /**
     * Show Avatar image (public)
     */
    public function image()
    {
        $user_id = $this->request->getIntegerParam('user_id');
        $size = $this->request->getStringParam('size', 48);
        $filename = $this->avatarFile->getFilename($user_id);
        $etag = md5($filename.$size);

        $this->response->cache(365 * 86400, $etag);
        $this->response->contentType('image/jpeg');

        if ($this->request->getHeader('If-None-Match') !== '"'.$etag.'"') {
            $this->render($filename, $size);
        } else {
            $this->response->status(304);
        }
    }

    /**
     * Render thumbnail from object storage
     *
     * @access private
     * @param  string  $filename
     * @param  integer $size
     */
    private function render($filename, $size)
    {
        try {
            $blob = $this->objectStorage->get($filename);

            Thumbnail::createFromString($blob)
                ->resize($size, $size)
                ->toOutput();
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
